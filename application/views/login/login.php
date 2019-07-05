<br><br>
<div class="login-div">
    <span class="title">Login into your account</span>
    <form method="post">
        <div class="input" data-validate = "Valid email is: a@b.c">
            <input type="text" placeholder="Username or Email" name="username" required>
        </div>
        <div class="input" data-validate="Enter password">
            <input type="password" placeholder="Password" name="password" required>
        </div>

        <div class="submit">
            <div class="container">
                <div></div>
                <button type="submit" name="login" value="Login">Login</button>
            </div>
        </div>
        <div id="forgot-password">
            <a href="<?= HOST_NAME ?>login/forgot_password" class="forgot">Forgot Password</a>
        </div>
    </form>

    <div class="footer">
        <span>Don't have account? <a href="<?= HOST_NAME ?>login/register" class="register">Create Account</a> </span>
    </div>
</div>

