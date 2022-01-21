<?php require_once "layout/head.php" ?>
<?php if (isset($_SESSION['logged_in'])) header('Location: /TomatoClock'); ?>

<div class="container">
    <div class="pt-3 mb-5 border-bottom">
        <a href="/TomatoClock" class="d-flex justify-content-center align-items-center text-dark text-decoration-none">
            <span class="material-icons" style="font-size:50px">alarm</span>
            <h1 style="font-size:50px;">Pomodoro</h1>
        </a>
    </div>
    <h2 class="text-center py-2">登入</h2>
    <div class="card mx-auto" style="max-width: 400px; width:100%">
        <form class="card-body d-flex flex-column">
            <h6 class="text-start">信箱</h6>
            <input class="form-control w-100" id="email" name="email" type="email" required="required">
            <h6 class="text-start mt-1">密碼</h6>
            <input class="form-control w-100" id="password" name="password" type="password" required="required">
            <h6 id="err_msg" class="text-danger mt-2"></h6>
            <a class="mt-2 me-auto" href="signup.php">註冊</a>
            <button class="btn btn-danger mt-2" type="submit">送出</button>
            <a id="GG_login" class="btn mt-3 border-dark text-decoration-none" href="google_login.php">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                </svg>
                Google 登入
            </a>
            <!-- <a id="FB_login" class="btn btn-primary mt-3 text-light text-decoration-none" href="fb_login.php">Facebook</a> -->
        </form>
    </div>
</div>
<script src="js/login.js"></script>

<?php require_once "layout/footer.php" ?>