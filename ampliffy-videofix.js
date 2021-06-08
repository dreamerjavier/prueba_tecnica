var targetDivAmpliffyID = "prueba-amp-target";
var evenCounter = 0;

function spotTarget() {
    var vidChildElement = $("#vid-695094266 div:first-of-type").first();
    vidChildElement.attr("id", targetDivAmpliffyID);
    var mut = new MutationObserver(function (mutations, mut) {
        if ((evenCounter % 2) != 1) {
            setNewVidSize();
            evenCounter++;
        } else {
            console.log("Counter: "+evenCounter);
            console.log(evenCounter % 2);
            evenCounter++;
        }

    });
    mut.observe(document.querySelector("#" + targetDivAmpliffyID), {
        'attributes': true
    })
}

function setNewVidSize() {
    var vidParentHeight = $("#vid-695094266").outerWidth();
    var vidChildElement = $("#" + targetDivAmpliffyID);
    console.log(vidParentHeight);
    var newVidChildClass = vidChildElement.attr("class").replace(/height-.*-/, 'height-' + ((Math.round(vidParentHeight * 0.57)) + 5) + '-');
    console.log(newVidChildClass);
    vidChildElement.attr("class", newVidChildClass);
}

$(window).load(function () {
    spotTarget();
    setNewVidSize();
});