<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage,Exception,Throwable,Log;

class ExtractController extends Controller
{
    public function extract(Request $request)
    {
        include('../database/connections/db.php'); 
        $targetDir = "public/files/";      
        $allowTypes = array('pdf','doc','docx');
        $path =  $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
        if(isset($_POST['mul_submit'])){
            if(!empty(array_filter($_FILES['files']['name']))){
                foreach($_FILES['files']['name'] as $key=>$val){                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                    // File upload path
                    $fileName = basename(/*$id."_".*/$_FILES['files']['name'][$key]);
                    // echo $fil= basename($fileName,".pdf");

                    $info = pathinfo($fileName);
                    $file_name =  basename($fileName,'.'.$info['extension']);
                    
                    $targetFilePath = $targetDir . $fileName;
                    
                    // Check whether file type is valid
                    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
                    if(in_array($fileType, $allowTypes)){
                        // Upload file to server
                        $path = Storage::putFileAs($targetDir, $_FILES["files"]["tmp_name"][$key],$fileName);
                        if($path){
                        // if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                            // Image db insert sql
                            $insertValuesSQL .= "('".$fileName."', NOW()),";/*for date nd time other option are like $date = date('Y-m-d H:i:s');*/
                        }
                        else{
                                $errorUpload .= $_FILES['files']['name'][$key].', ';
                            }
                    }
                    else{
                            $errorUploadType .= $_FILES['files']['name'][$key].', ';
                    }
                    
                }
                    if(!empty($insertValuesSQL)){
                        $insertValuesSQL = trim($insertValuesSQL,',');
                        // Insert image file name into database
                        $insert = $conn->query("INSERT INTO files (file_name, uploaded_on) VALUES $insertValuesSQL");
                        if($insert){
                            $errorUpload = !empty($errorUpload)?'Upload Error: '.$errorUpload:'';
                            $errorUploadType = !empty($errorUploadType)?'File Type Error: '.$errorUploadType:'';
                            $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType;
                            $statusMsg = "Files are uploaded successfully.".$errorMsg;
                        }else{
                            $statusMsg = "Sorry, there was an error uploading your file.";
                        }
                    } 
                }
                else{
                    $statusMsg = 'Please select a file to upload.';
                }
                
                
                // Display status message
                // echo $statusMsg;
                ini_set('max_execution_time', 300);
                set_time_limit(300);
                try {
                    $output=system("cd ../ && python extractor.py");
                    // return $output;
                    // $output=system("cd ../ && ex-win\scripts\activate && python extractor.py && deactivate");
                    // return $output;
                    Log::notice($output);
                    $request->session()->flash('alert-info', "Excel sheet created succesfully");
                } 
                
                catch (Throwable $e) {
                    $request->session()->flash('alert-danger', "Error extracting details");
                    dd($e);
                    report($e);
                } 
                $request->session()->flash('alert-success', $statusMsg);
                return redirect()->back();
        }
        if(isset($_POST['download'])){
          if(Storage::exists("resume_data.xls")){
                $request->session()->flash('alert-success', "Downloaded the file");
               return Storage::download("resume_data.xls");                        
            }
            else{
                $request->session()->flash('alert-warning', "Some error please try again");
                return redirect()->back();
            }
        }   
    }
    
}
