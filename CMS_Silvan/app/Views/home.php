        
            <main id="home-area" class="col-12 p-2 mt-5">
                <div id="teaser-div" class="row d-flex justify-content-around">
                    <div id="teaser-text" class="col-lg-4 p-4">
                        <h1 class="display-3">Zusammen wohnen, aber fair und übersichtlich!</h1>
                        <p>Das Ziel von FlatPeace ist es, das Zusammenleben in einer Wohngemeinschaft (WG) mit allen seinen Aufgaben und Pflichten übersichtlich und fair zu gestalten.</p>
                        <?php if(!$user->isLoggedIn()) : ?>
                            <a href="<?= $root ?>/register" class="btn btn-primary submit-btn-color">Registrieren</a>
                            <a href="<?= $root ?>/login" class="btn btn-secondary">Login</a>
                        <?php else : ?>
                            <a href="<?= $root ?>/flat" class="btn btn-primary submit-btn-color">Meine WG</a>
                        <?php endif; ?>
                    </div>
                    <div id="teaser-picture" class="col-lg-6 d-flex justify-content p-4">
                        <img src="<?= $root ?>/images/static/teaser.jpg" alt="">
                    </div>
                </div>
            </main>   
        </div>