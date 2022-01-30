            <div id="task-wrap" class="d-flex">    
                <main class="meine-aufgaben-main col-xs-12 col-lg-9 p-2">
                    <div id="list-container">
                        <h1 class="text-start m-2 p-2">Meine Aufgaben</h1>

                        <div id="tasks-label" class="w-88 list-line d-flex align-items-end justify-content-between">
                            <div id="task-title-label" class="w-25">Titel</div>
                            <div id="task-creator-label" class="w-25">Erstellt von</div>
                            <div id="task-staff-label" class="w-25">Zugeteilt</div>
                            <div id="task-deadline-label" class="w-25">Deadline</div>
                        </div>
                        
                        <?php foreach($tasks as $task) : ?>
                            <div class="task-list-item w-88 list-line d-flex align-items-center justify-content-between" data-taskid="<?= $task[ "id" ] ?>">
                                <div class="task-title w-25 p-2"><?= $task['title'] ?></div>
                                <div class="task-creator w-25 p-2"><?= $task['alias'] ?></div>
                                <div class="task-staff w-25 p-2">
                                    |
                                    <?php 
                                    foreach($task['assigned'] as $alias) { 
                                        echo $alias['alias'] . ' | '; 
                                    } ?>
                                </div>
                                <div class="task-deadline w-25 p-2"><?= $task['deadline'] ?></div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </main>    
                <aside id="detailbar" class="col-3 p-1 bg-light justify-content-center rounded d-flex position-fixed">
                    <!-- Content: async tasks.js -->
                </aside>
            </div>
        </div>
        <script src="<?= $root ?>/js/tasks.js"></script>