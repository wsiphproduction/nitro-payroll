
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>.:: NITRO PAYROLL ::. Forgot Password</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NITRO Payroll">
    <meta name="author" content="NITRO Payroll">

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- MAIN CSS -->
      <link rel="stylesheet" href="{{URL::asset('public/web/css/admin-login.css')}}">
</head>

<body>

<div id="layout" class="theme-orange">
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="d-flex h100vh align-items-center auth-main w-100">
            <div class="auth-box">
                <div class="top mb-4">
                    <div class="logo">
                         <img src="{{URL::asset('public/img/PMC-Company-Logo.png')}}">
                    </div>
                </div>
                <div class="card shadow p-lg-4">
                    <div class="card-header">
                        <p class="fs-5 mb-0">Recover my password</p>
                    </div>
                    <div class="card-body">
                        <p>Please enter your email address below to receive instructions for resetting password.</p>
                        <form action="index.html">
                            <div class="form-floating mb-1">
                                <input type="email" class="form-control" placeholder="name@example.com">
                                <label>Email address</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 px-3 py-2">RESET PASSWORD</button>
                            <div class="text-center mt-3">
                                <span class="helper-text">Already have your password? <a href="{{URL::route('home')}}">Login</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER -->
</div>
</body>
</html>