<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class LoginController extends Controller
{
    //
    public function authenticate(Request $request)
    {

        require_once '../database/connections/db.php';
        $email= $request->email;
        $password= $request->password;
        $login=route('login');
        $sql=mysqli_query($conn,"SELECT 1 FROM USERS where email='$email'");
        if($sql){
            $row=mysqli_fetch_array($sql);
            if($row){ 
                $sql=mysqli_query($conn,"SELECT * FROM USERS where email='$email'");
                $row=mysqli_fetch_array($sql);
                if(password_verify($password, $row["password"])){
                    $id=$row['id'];
                    $url=route('dashboard',$id);
                    echo "<script>location.replace('$url');</script>";
                }
                else{
                    echo "<script>alert('Wrong password try again');location.replace('$login');</script>";
                }
            }
            else{
                echo "<script>alert('Email doesnt exist please check again');location.replace('$login');</script>";
            }
        }
        else{
            echo "<script>alert('Some error please try again');location.replace('$login');</script>";
        }

        
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return view('index');
    }   
    public function register(Request $request)
    {
        if(isset($_POST["signup"])){
            require_once '../database/connections/db.php';
            $name=$_POST['name'];
            $email=$_POST['email'];
            $password=$_POST['password'];
            $conf_password=$_POST['confirm_password'];
            $sql=mysqli_query($conn,"SELECT 1 FROM USERS where email='$email'");
            $row=mysqli_fetch_array($sql);
            if($row){
                $url=route('register');
                echo "<script>alert('Email already exists');
                       location.replace('$url');</script>";
            }
            elseif($password==$conf_password){
                $password=password_hash($password, PASSWORD_DEFAULT);
                $sql=mysqli_query($conn,"INSERT INTO `users`( `name`, `email`, `password`) VALUES ('$name','$email','$password')");
                if($sql){
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
        if(isset($_POST["forgotPassword"])){
            $email = trim($_POST["email"]);
            $query = $conn->prepare("SELECT id FROM users  WHERE email =?");
            $query->execute(array($email));
            $user = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($user))
            {
                $hours=2;
                $url=URL::temporarySignedRoute('resetPassword',now()->addHours($hours),['id'=>$user[0]['id']]);
                $users=$user[0]['first_name'];         
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
        include('../database/connections/db.php');
        $query = "
            SELECT IF(req_subscribe, True, False) req_subscribe,IF(subscribe, True, False) subscribe,download from admin_login 
                WHERE admin_email = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $subscribe=$row['subscribe'];
        $req_subscribe=$row['req_subscribe'];
        $downloads=$row['download'];
        $stmt->close();
   
    }
}
