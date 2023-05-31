<!DOCTYPE html>
<?php
include "db.php";
session_start();
if(isset($_SESSION['name'])){
    header("location:welcome.php");
}
$errors=array();
#user data validation
function validation_Data($name,$last_name,$email,$password,$re_password){
    global $errors;
    global $connection;
    $special_character=("! @ # $ % ^ & * = - +");
    #Name validation
    if(empty($name) or ctype_space($name)){
        array_push($errors,"نام نمیتواند خالی باشد");
       
        
    }
    else if(strlen($name)<=4){
        array_push($errors,"نام باید بیشتر از دو حرف باشد");
        
    }
    else if(preg_match("/[!@#$%^&*123456789]/",$name)){
        array_push($errors,"نام نمیتواند شامل کاراکتر های خاص و اعداد باشد");
    }
    #Lastname validation
    if(empty($last_name)==false){
        if(strlen($last_name)<=4){
            array_push($errors,"نام خانوادگی باید بیشتر از دو حرف باشد");
        }
        else if(preg_match("/[!@#$%^&*123456789]/",$last_name)){
            array_push($errors,"نام خانوادگی نمیتواند شامل کاراکتر های خاص و اعداد باشد");
        }
    }
    #Email validation
    if( empty($email)){
       
        array_push($errors,"ایمیل نمیتواند خالی باشد");
        
    }
    else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        array_push($errors,"فرمت ایمیل را صحیح وارد کنید");
        
    }
    #checking if the email exists in the database or not
    else{
        $result = mysqli_query($connection, "SELECT * FROM users WHERE email='$email'");
        $row=mysqli_fetch_assoc($result);
        if($row){
            if($row['email']==$email){
                array_push($errors,"این ایمیل ثبت شده از یک ایمیل دیگر استفاده کنید");
                }
        }

                
    }
    #Password validation
    if(empty($password)){
        array_push($errors,"پسورد نمیتواند خالی باشد");
    }
    else if(strlen($password)<6){
        array_push($errors,"پسور باید حداقل 6 کاراکتر باشد");

    }
    else if($password !== $re_password){
        array_push($errors,"پسورد با تکرار ان مطابقت ندارد");
    }
}


#image validation
function validation_Image($img){
    global $errors;
    if($img['error']===0){
        $img_name=$img['name'];
        $img_tmp_name=$img['tmp_name'];
        $img_size=$img['size'];
        #checking image size
        if($img_size>=2000000){
            array_push($errors,"سایز تصویر نمیتواند بیشتر از 2 مگ باشد");
        }else{
            #get image extension.for example jpg or png.
            $img_extension=pathinfo($img_name,PATHINFO_EXTENSION);
            #extension allowed to upload
            $extension_allowed=['png','jpeg','jpg'];
            if(in_array($img_extension,$extension_allowed)){
                $img_name_new=uniqid("img-",true).'.'.$img_extension;
                $img_upload_path="uploads/".$img_name_new;
                move_uploaded_file($img_tmp_name,$img_upload_path);
                return $img_name_new;
            }else{
                array_push($errors,"تصویر تنها باید یکی از فرمت های png,jpeg,jpg باشد.");
            }
        }
    }else{
        array_push($errors,"تصویری اپلود نشده لطفا تصویر را اپلود کنید");
    }

}

#get data from user
if(isset($_POST['submit'])){
    $name=trim($_POST['name']);
    $last_name=trim($_POST['last_name']);
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);
    $re_password=trim($_POST['re_password']);
    $img=$_FILES['img'];
    #Check user data
    validation_Data($name,$last_name,$email,$password,$re_password);
    #if user data valid then check image file
    if(empty($errors)){
        #chech image file,if valid then get image file name
        $image_name=validation_Image($img);
        #when there were no errors insert data to database
        if(empty($errors)){
            #increasing database security
            $nmae=mysqli_real_escape_string($connection,$name);
            $last_name=mysqli_real_escape_string($connection,$last_name);
            $email=mysqli_real_escape_string($connection,$email);
            $password=mysqli_real_escape_string($connection,$password);
            #password hash
            $password=hash("md5",$password,false);
            $query="INSERT INTO users(id,name,last_name, email, password,image) VALUES (id,'$name','$last_name', '$email', '$password','$image_name')";
            $result=mysqli_query($connection,$query);
            if($result){
                #create session name
                $_SESSION['name']=$name;
                #redirect to welcome.php
                header("Location: welcome.php
                ");
       
                }else{
                array_push($errors,"خطا");
            }
                }
            }

}
?>

 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
 </head>
 <style>
    body {
        direction: rtl;
        text-align: right;
    }
    .red{
        color: red;
    }
    </style>
 <body class="bg-light">


   

    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="card mt-4 p-4 w-50">
                <H2 class="text-center p-4">فرم ثبت نام</H2>
                <form action="index.php" method="post" enctype="multipart/form-data">

                    <input class="form-control" type="text" name="name" value="<?= $name??'' ?>" placeholder="نام">
                    <br>
                    <br>
                    <input class="form-control" type="text" name="last_name" value="<?= $last_name??'' ?>"  placeholder="نام خانوادگی">
                    <br>
                    <br>
                    <input class="form-control" type="email" name="email" value="<?= $email??'' ?>" placeholder="ایمیل">
                    <br>
                    <br>
                    <input class="form-control" type="password" name="password" placeholder="پسورد">
                    <br>
                    <br>
                    <input class="form-control" type="password" name="re_password" placeholder="تکرار پسورد">
                    <br>
                    <br>
                    <input class="form-control" type="file" name="img" >
                    <br>
                    <br>
                    <input class="btn btn-success form-control " type="submit" name="submit" value="ثبت نام">
                </form>
                
                <?php
            if(empty($errors)==false){
                foreach($errors as $error){
                    echo "<li class='red p-2'>$error</li>";
                }
            }
            ?>

            </div>
        </div>
    </div>

    
 </html>