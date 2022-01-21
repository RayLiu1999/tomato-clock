<?php require_once "layout/head.php" ?>
<?php if (isset($_SESSION['logged_in'])) header('Location: /TomatoClock'); ?>

<div class="container">
    <div class="pt-3 mb-5 border-bottom">
        <a href="/TomatoClock" class="d-flex justify-content-center align-items-center text-dark text-decoration-none">
            <span class="material-icons" style="font-size:50px">alarm</span>
            <h1 style="font-size:50px;">Pomodoro</h1>
        </a>
    </div>
    <h2 class="text-center py-2">註冊</h2>
    <div class="card mx-auto" style="max-width: 400px; width:100%">
        <form class="card-body d-flex flex-column">
            <h6 class="text-start">信箱</h6>
            <input class="form-control w-100" id="email" name="email" type="email" required="required">
            <h6 class="text-start mt-1">密碼</h6>
            <input class="form-control w-100" id="password" name="password" type="password" required="required">
            <h6 class="text-start mt-1">確認密碼</h6>
            <input class="form-control w-100" id="confirm_password" name="confirm_password" type="password" required="required">
            <h6 id="err_msg" class="text-danger mt-2"></h6>
            <button class="btn btn-danger mt-2" type="submit" id="submit">送出</button>
        </form>
    </div>
</div>
<script src="js/signup.js"></script>

<?php require_once "layout/footer.php" ?>