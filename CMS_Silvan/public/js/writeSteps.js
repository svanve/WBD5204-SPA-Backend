const stepCount = document.querySelector('#step-count-btn');
var stepsDiv = document.querySelector('#steps-div');
var stepsArr = stepsDiv.children;
var counter = 0;
var divString;

var step1 = document.querySelector('#step1'),
    step2 = document.querySelector('#step2'),
    step3 = document.querySelector('#step3'),
    step4 = document.querySelector('#step4'),
    step5 = document.querySelector('#step5'),
    step6 = document.querySelector('#step6'),
    stepsArr = [ step1, step2, step3, step4, step5, step6 ];

if(stepsArr[0] === null) {
    ++counter;
} 

stepsArr.forEach( (el) => {
    if(el !== null) {
        ++counter;
    }
});

stepCount.addEventListener('click', generateStep);    

function generateStep(event) {
    event.preventDefault();

    if(counter < 6) {
        ++counter;
    } else {
        return;
    }

    for (let i = 1; i < counter; i++) {
        let divStringRaw = '<label for="stepCOUNTER" class="mt-2 form-label">Zwischenschritt COUNTER</label>' + '<textarea name="tasks_stepCOUNTER" class="form-control" id="stepCOUNTER" placeholder="Zwischenschritt eingeben"></textarea>';
        divString = divStringRaw.replaceAll('COUNTER', `${i+1}`);
    }

    stepsDiv.innerHTML += divString;

    window.scrollTo(0,document.body.scrollHeight);
}
