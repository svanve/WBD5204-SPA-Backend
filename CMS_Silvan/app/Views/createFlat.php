            <main class="col-10 p-2 mt-5">
                <h1 class="text-start p-2 col-9 m-0-auto">Meine WG</h1>
                <p class="text-start p-2 col-9 mb-3 m-0-auto">Gründe hier eine WG und lade deine Mitbewohner ein. Sollte bereits eine WG gegründet worden sein, kontaktiere den/die Gründer/in der WG-Gruppe. Die Person kann dich - ebenfalls - unter dem Reiter "Meine WG" hinzufügen.</p>

                <h2 class="text-start p-2 col-9 m-0-auto">WG gründen</h2>
                <form id="wg-register-form" method="post" class="col-9 p-2 mb-4 m-0-auto">
                    <?php if (isset($errors['root'])): ?>
                        <div class="error alert alert-danger"><?=$errors['root']?></div>
                    <?php endif; ?>
                    <div class="form-group mb-2">
                        <label for="flat-name" class="form-label">WG-Name</label>
                        <input type="text" class="form-control" id="flat-name" name="name" placeholder="Wähle einen Namen für deine WG">
                        <?php if (isset($errors['name'])): ?>
                            <div class="error alert alert-danger"><?=$errors['name'][0]?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group mb-3">
                        <label for="flats-members" class="form-label">WG-Bewohner</label>
                        <input type="text" class="form-control" id="flats-members" name="flats_members" placeholder='Mitbewohner1, Mitbewohner2, Mitbewohner3'>
                        <?php if (isset($errors['flats_members'])): ?> 
                            <div class="error alert alert-danger"><?=$errors['flats_members'][0]?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" value="" class="btn btn-primary">
                        <span class="submit-btn-color">WG gründen</span>
                    </button>
                </form>
            </main>
        </div>