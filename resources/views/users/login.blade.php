<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <title>Login Page</title>
</head>
<body>
    <div class="login-container d-flex align-items-center justify-content-center">
        <form action="/logins" class="login-form text-center" method='post'>
            
        <a href="{{route('home')}}"><h1 class="mb-5 font-weight-light text-uppercase">Data Extractor Login</h1></a>
            <div class="form-group">
                <input type="email" name="email" id="username" class="form-control rounded-pill form-control-lg" placeholder="Email">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control rounded-pill form-control-lg" placeholder="Password">
            </div>
            
            <div class="forgot-link d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input type="checkbox" name="checkbox" id="remember" class="form-check-input">
                    <label for="remember">Remember Password</label>
                </div>
                <a href="{{route('forgotPassword')}}">Forgot Password?</a>
            </div>

            <button type="submit" class="btn mt-5 btn-primary btn-custom btn-block text-uppercase rounded-pill btn-lg" name="login">Login</button>
            <p class="mt-3 font-weight-normal">Don't Have an account? <a href="{{route('register')}}"><strong>Register Here</strong></a></p>
        @csrf
        </form>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>
