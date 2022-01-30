            <div id="task-wrap" class="d-flex">    
                <main class="meine-aufgaben-main col-xs-12 col-lg-9 p-2">
                    <h1 class="m-2 p-2"><?= $name ?></h1>       
                    <ul id="flat-members-container" class="m-3 list-group w-auto">
                        <li class="li-title p-3 list-group-item bg-light">WG-Bewohner</li>
                        <?php foreach ($flatMembers as $singleUser): ?> 
                            <li class="list-group-item"><?= $singleUser ?></li>   
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if ( $isAdmin === true ): ?>
                        <ul id="flat-input-container" class="m-3 list-group w-auto">
                            <li class="li-title p-3 list-group-item bg-light">
                                <form method="post">
                                    <label class="mb-2" for="add-mate">Füge einen zusätzlichen WG-Bewohner über seinen Usernamen hinzu.</label>
                                    <div class="d-flex">
                                        <input id="add-mate" class="p-2" type="text" name="add_mate" placeholder="Bewohner hinzufügen">
                                        <input type="submit" class="btn btn-primary submit-btn-color ms-3" value="Bewohner hinzufügen">
                                    </div>
                                </form>
                            </li>
                        </ul>
                    <?php endif; ?>

                    <div id="list-container">
                        <h2 class="text-start m-2 mt-3 p-2">Tasks von <?= $name ?></h2>

                        <div id="tasks-label" class="w-88 list-line d-flex align-items-end justify-content-between">
                            <div id="task-title-label" class="w-25">Titel</div>
                            <div id="task-creator-label" class="w-25">Erstellt von</div>
                            <div id="task-staff-label" class="w-25">Zugeteilt</div>
                            <div id="task-deadline-label" class="w-25">Deadline</div>
                        </div>
                        
                        <?php foreach($flatTasks as $task) : ?>
                            <div class="task-list-item w-88 list-line d-flex align-items-center justify-content-between" data-taskid="<?= $task[ "id" ] ?>">
                                <div class="task-title w-25 p-2"><?= $task['title'] ?></div>
                                <div class="task-creator w-25 p-2"><?= $task['alias'] ?></div>
                                    <div class="task-staff w-25 p-2">
                                    |
                                    <?php foreach($task['assigned'] as $alias) { 
                                        echo $alias['alias'] . ' | '; 
                                    } ?>
                                </div>
                                <div class="task-deadline w-25 p-2"><?= $task['deadline'] ?></div>
                            </div>
                        <?php endforeach;?>

                    </div>
                </main>    
                <aside id="detailbar" class="col-3 p-1 bg-light justify-content-center rounded d-flex position-fixed">
                    <!-- Content by FlatController -->
                </aside>
            </div>
        </div>
        <script src="<?= $root ?>/js/flat.js"></script>

