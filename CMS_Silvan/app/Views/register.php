            <main class="col-10 p-2 mt-5">    
                <h1 class="text-start p-2 col-9 m-0-auto">Registrieren</h1>

                <form method="post" class="col-9 p-2 m-0-auto">
                    <div class="form-group mb-2">
                        <label for="alias" class="form-label">Username</label>
                        <input type="text" id="alias" name="alias" class="form-control" placeholder="Gib deinen gewünschten Usernamen ein.">
                        <?php if (isset($errors['alias'])): ?>
                            <div class="error alert alert-danger"><?=$errors['alias'][0]?></div>
                        <?php endif; ?>                  
                    </div>

                    <div class="form-group mb-2">
                        <label for="email" class="form-label">Email-Addresse</label>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Gib deine Mailadresse ein.">
                        <?php if (isset($errors['email'])): ?>
                            <div class="error alert alert-danger"><?=$errors['email'][0]?></div>
                        <?php endif; ?>                  
                    </div>

                    <div class="form-group mb-2">
                        <label for="password" class="form-label">Passwort</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Wähle ein Passwort.">
                        <?php if (isset($errors['password'])): ?>
                            <div class="error alert alert-danger"><?=$errors['password'][0]?></div>
                        <?php endif; ?>                  
                    </div>

                    <div class="form-group mb-3">
                        <label for="password-again" class="form-label">Passwort wiederholen</label>
                        <input type="password" id="password-again" name="passwordAgain" class="form-control" placeholder="Wiederhole das gewählte Passwort.">
                        <?php if (isset($errors['passwordAgain'])): ?>
                            <div class="error alert alert-danger"><?=$errors['passwordAgain'][0]?></div>
                        <?php endif; ?>                  
                    </div>

                    <input type="submit" value="Registrieren" class="btn btn-primary submit-btn-color">
                </form>
            </main>
        </div>
