<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

use PDO,URL,Mail,Exception,Throwable,Storage,Log;
use App\Mail\ForgotPassword;
class LoginController extends Controller
{
    //
    public function authenticate(Request $request)
    {

        require_once '../database/connections/db.php';
        $email= $request->email;
        $password= $request->password;
        $login=route('login');
        $stmt=$conn->prepare("SELECT 1 FROM users where email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        
        if($stmt){
            $res = $stmt->get_result();
            $res = $res ->fetch_assoc();
            if($res){ 
                $sql=$conn->prepare("SELECT * FROM users where email=?");
                $sql->bind_param("s",$email);
                $sql->execute();
                $res = $sql->get_result();
                $row = $res->fetch_assoc();
                if(password_verify($password, $row["password"])){
                    $id=$row['id'];
                    $conn->close();
                    $url=route('dashboard',$id);
                    echo "<script>location.replace('$url');</script>";
                }
                else{
                    $conn->close();
                    echo "<script>alert('Wrong password try again');location.replace('$login');</script>";
                }
            }
            else{
                $conn->close();
                echo "<script>alert('Email doesnt exist please check again');location.replace('$login');</script>";
            }
        }
        else{
            $conn->close();
            echo "<script>alert('Some error please try again');location.replace('$login');</script>";
        }

        
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return redirect()->route('home');
    }   
    public function register(Request $request)
    {
        if(isset($_POST["signup"])){
            require_once '../database/connections/db.php';
            $name=htmlspecialchars($request->name);
            $email=$request->email;
            $password=$request->password;
            $conf_password=$request->confirm_password;

            $stmt=$conn->prepare("SELECT 1 FROM users where email=?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $res=$stmt->get_result();
            $res=$res->fetch_assoc();
            if($res){
                $url=route('register');
                echo "<script>alert('Email already exists');
                       location.replace('$url');</script>";
            }
            elseif($password==$conf_password){
                $password=password_hash($password, PASSWORD_DEFAULT);
                $sql=$conn->prepare("INSERT INTO `users`( `name`, `email`, `password`) VALUES (?,?,?)");
                $sql->bind_param("sss",$name,$email,$password);
                $sql->execute();
                if($sql->affected_rows==1){
                    $url=route('login');
                    echo "<script>alert('Credentials created redirecting to login');
                           location.replace('$url');</script>";
                }
                else{
                    echo "<script>alert('Not able to create account please try again');</script>";
                }
            }
            else{
                echo "<script>alert('Please enter confirm password same as password');</script>";
            }
        }
    }
    public function forgotPassword(Request $request)
    {
        require_once '../database/connections/db.php';
        if(isset($_POST["forgot"])){
            $email = trim($_POST["email"]);
            $sql=$conn->prepare("SELECT * FROM users where email=?");
                $sql->bind_param("s",$email);
                $sql->execute();
                $res = $sql->get_result();
                $user = $res->fetch_assoc();
            if(!empty($user))
            {
                $hours=2;
                $url=URL::temporarySignedRoute('resetPassword',now()->addHours($hours),['id'=>$user['id']]);
                $users=$user['name'];         
                $data=[
                    'user'=>$users,
                    'hours'=>$hours,
                    'url'=>$url
                ];

                    try
                    {
                        Mail::to($email)->send(new ForgotPassword($data));
                        $success_message = "Successfully sent , Reset password link provided on your mail !";	
                        $request->session()->flash('alert-success', $success_message);
                    }
                    catch (Throwable $e) {
                        report($e);
                        $error_message = "Mailer Error : Please try again";
                        $request->session()->flash('alert-warning', $error_message);
                    } 

            } 
            else {
                $error_message = 'No Email Found';
                $request->session()->flash('alert-warning', $error_message);
            }
            return redirect()->back();
        }
        
    }
    public function resetPassword(Request $request)
    {
        require_once '../database/connections/db.php';
        if(isset($_POST["reset"])){
            $id = (int)$request->id;
            $password = trim($_POST["password"]);
            $confirmPassword = trim($_POST["confirmPassword"]);
            if($password == $confirmPassword) {
            $password = password_hash($password, PASSWORD_DEFAULT); 
            // $stmt = mysqli_query($conn,"UPDATE users SET password= '$password' WHERE id = '$id'");
            $sql=$conn->prepare("UPDATE users SET password=? WHERE id = ?");
            $sql->bind_param("si",$password,$id);
            $sql->execute();
            if($sql->affected_rows==1) {
                $url=route('login');
                $success_message = "Password is reset successfully.<br>Now you are redirecting";
                return "<script>alert('$success_message');location.replace('$url');</script>";
            } 
            else {
                // dd($sql->affected_rows);
                $error_message = "Failed : <br> Password not updated";
                $request->session()->flash('alert-warning', $error_message);
                }
            }//if same password 
            else {
                $error_message = "Password not matched";
                $request->session()->flash('alert-warning', $error_message);
            }
            return redirect()->back();
        }
        else{
            $error_message = "Some error please try again";
            $request->session()->flash('alert-warning', $error_message);
        }
        return redirect()->back();
   
    }
}
