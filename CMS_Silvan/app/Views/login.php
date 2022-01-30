            <main class="col-10 p-2 mt-5">
                <h1 class="text-start p-2 col-9 m-0-auto">Login</h1>

                <form id="login-form" method="post" class="col-9 p-2 m-0-auto">
                    <?php if (isset($errors['root'])): ?>
                        <div class="error alert alert-danger"><?=$errors['root']?></div>
                    <?php endif; ?>
                    <div class="form-group mb-2">
                        <label for="email" class="form-label">Mailadresse</label>
                        <input type="text" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Gib deine Mailadresse ein.">
                        <?php if (isset($errors['email'])): ?>
                            <div class="error alert alert-danger"><?=$errors['email'][0]?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Passwort</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Gib dein Passwort ein.">
                    </div>
                    <?php if (isset($errors['password'])): ?> 
                        <div class="error error-last alert alert-danger mb-3"><?=$errors['password'][0]?></div>
                    <?php endif; ?>
                    <button type="submit" value="" class="btn btn-primary">
                        <span class="submit-btn-color">Login</span>
                    </button>
                </form>
            </main>
        </div>

