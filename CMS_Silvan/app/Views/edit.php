            <main id="write-area" class="col-10 p-2 mt-5">
                <h1 class="text-start p-2 col-9 m-0-auto">Task bearbeiten</h1>

                <?php if (isset($errors['root'])): ?>
                    <div class="error"><?=$errors['root']?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" id="form-write" class="col-9 m-0-auto p-2">
                    <div class="form-group mb-2">
                        <?php if (isset($errors['title'])): ?>
                            <div class="error alert alert-danger"><?=$errors['title'][0]?></div>
                        <?php endif; ?>
                        <label for="title" class="form-label">Titel*</label>
                        <input type="text" id="title" class="form-control" name="title" value="<?= $taskData['main_data']['title'] ?>">
                    </div>
                    
                    <div class="form-group mb-2">
                        <?php if (isset($errors['body'])): ?>
                            <div class="error alert alert-danger"><?=$errors['body'][0]?></div>
                        <?php endif; ?>
                        <label for="body" class="form-label">Inhalt*</label>
                        <textarea name="body" class="form-control" id="body"><?= $taskData['main_data']['body'] ?> </textarea>
                    </div>
                    <div class="form-group mb-2">
                        <?php if (isset($errors['deadline'])): ?>
                            <div class="error alert alert-danger"><?=$errors['deadline'][0]?></div>
                        <?php endif; ?>
                        <label for="deadline" class="form-label">Deadline*</label>
                        <input type="date" id="deadline" class="form-control" name="deadline" value="<?= $taskData['main_data']['deadline'] ?>">
                    </div>
                    <div class="form-group mb-2">
                        <?php if (isset($errors['assigned'])): ?>
                            <div class="error alert alert-danger"><?=$errors['assigned'][0]?></div>
                        <?php endif; ?>
                        <label for="assigned" class="form-label">Zuständig*</label>
                        <input type="text" id="assigned" class="form-control" name="assigned" value="<?php foreach ($taskData['assigned_users'] as $alias) { echo $alias['alias'] . ', '; } ?>">
                    </div>
                    <div class="form-group mb-2">
                        <?php if (isset($errors['image'])): ?>
                            <div class="error alert alert-danger"><?=$errors['image'][0]?></div>
                        <?php endif; ?>
                        <label for="image" class="form-label">Bild</label>
                        <input type="file" id="image" class="form-control" name="image">
                    </div>
                    <div class="position-relative">
                        <?php if (isset($errors['tasks_step'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step'][0]?></div>
                        <?php elseif (isset($errors['tasks_step2'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step2'][0]?></div>
                        <?php elseif (isset($errors['tasks_step3'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step3'][0]?></div>
                        <?php elseif (isset($errors['tasks_step4'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step4'][0]?></div>
                        <?php elseif (isset($errors['tasks_step5'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step5'][0]?></div>
                        <?php elseif (isset($errors['tasks_step6'])): ?>
                            <div class="error alert alert-danger"><?=$errors['tasks_step6'][0]?></div>
                        <?php endif; ?>
                        <div id="steps-div" class="form-group mb-3">
                            <?php foreach($taskData['tasks_steps'] as $i => $step): ?>
                                    <label for="step<?= $i+1 ?>" class="mt-2 form-label">Zwischenschritt <?= $i+1 ?></label>
                                    <textarea class="form-control" name="tasks_step<?= $i+1 ?>" id="step<?= $i+1 ?>"><?= $taskData['tasks_steps'][$i]['body'] ?></textarea>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <input type="submit" class="btn btn-primary submit-btn-color" value="Task überschreiben">
                </form>
                <div id="step-count-btn-wrap" class="col-9 m-0-auto p-2 d-flex align-items-center">
                    <div id="step-count-btn" class="btn btn-light">
                        <i class="fas fa-plus"></i>
                    </div>
                    <p class="m-0 p-2">Zwischenschritt hinzufügen</p>
                </div>

                <script defer src="<?=$root?>/js/writeSteps.js"></script>
            </main> 
        </div>