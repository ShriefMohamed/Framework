<br><br>
<div class="login-div" id="register-div">
    <span class="title">Create new account</span>

    <form method="post">
        <div class="input">
            <input type="text" name="first_name" placeholder="First Name" required>
        </div>
        <div class="input">
            <input type="text" name="sur_name" placeholder="Sur Name" required>
        </div>
        <div class="input">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input">
            <input type="email" name="confirm_email" placeholder="Confirm Email" required>
        </div>
        <div class="input" data-validate="Enter password">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="input" data-validate="Enter password">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <div class="input">
            <input type="number" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="gender-div">
            <div class="gender">
                <label>Male</label>
                <input type="radio" name="gender" value="male" required>
            </div>
            <div class="gender female">
                <label>Female</label>
                <input type="radio" name="gender" value="female" required>
            </div>
        </div>
        <div class="input">
            <label>Birthday</label>
            <input type="date" name="birth_date" required>
        </div>

        <div class="submit">
            <div class="container">
                <div></div>
                <button type="submit" name="register" value="Register">Register</button>
            </div>
        </div>
    </form>

    <div class="register-footer footer">
        <span>Already have an account! <a href="<?= HOST_NAME ?>login/login" class="register">Login into your account</a> </span>
    </div>
</div>
<br><br>