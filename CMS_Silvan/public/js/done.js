var taskListItems = document.querySelectorAll( ".task-list-item" );

var taskid = [];

for (let i = 0; i < taskListItems.length; i++) {
    taskid[i] = taskListItems[i].dataset.taskid;
    taskListItems[i].addEventListener('click', (e) => {
        showDetail( taskid[i] );
    })
}

function showDetail(taskid) {
    const detailbar = document.querySelector('#detailbar');

    if(window.outerWidth < 992) {
        window.scrollTo(0, detailbar);   
    }

    fetch('http://localhost:8888/CMS_Silvan/public/tasks/detail/' + taskid)
        .then(response => response.json())
        .then(data => {
            var cardHTML =  `<div class="card w-100 overflow-scroll">
                            <img class="card-img-top" src="images/${data.image}" alt="Kein Bild vorhanden">
                            <div class="card-body">
                                <h5 class="card-title">${data.title}</h5>
                                <p class="card-text">${data.body}</p>
                            </div>`
            
            data.steps.forEach(
                (step) => {
                    cardHTML +=     `<ul class="list-group list-group-flush">
                                        <li class="list-group-item">${step.body}</li>
                                    </ul>`
                }
            )

            cardHTML +=     `<div class="detail-btn-wrap d-flex col-10 mt-3 mb-3 m-0-auto">
                                <a href="http://localhost:8888/CMS_Silvan/public/done/uncheck/${data.id}" id="task-check-btn" class="btn btn-secondary col-3 submit-btn-color" title="Aufgabe wieder öffnen">
                                    <i class="fas fa-redo"></i>
                                </a>
                                <div class="col-1"></div>
                                <a href="http://localhost:8888/CMS_Silvan/public/done/delete/${data.id}" class="btn btn-danger col-3" title="Aufgabe löschen">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>`

            var template = document.createElement('template');
            template.innerHTML = cardHTML;

            detailbar.innerHTML = '';
            detailbar.append(template.content.firstChild);
        })
        .catch(err => console.error(err));
}

// Visualization of current/selected task showed in detail by giving bgcolor:
var tasks = document.querySelectorAll('.task-list-item');

tasks.forEach(function(task) {
    task.addEventListener('click', (e) => activate(e))
});

function activate(e) {
    e.stopPropagation();
    
    if(detailbar.classList.contains('d-none')) {
        detailbar.classList.remove('d-none');
        detailbar.classList.add('d-flex');
    }

    tasks.forEach(function(task) {
        task.classList.remove('activated');
    });
    
    target = e.currentTarget;
    target.classList.add('activated');
}