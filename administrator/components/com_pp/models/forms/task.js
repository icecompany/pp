'use strict';
Joomla.submitbutton = function (task) {
    let form = document.querySelector('#adminForm');
    let valid = document.formvalidator.isValid(form);
    if (task === 'task.cancel' || valid) {
        let fields = document.querySelectorAll("#adminForm input[type='text']");
        fields.forEach(function(elem) {
            elem.value = elem.value.trim();
            elem.value = elem.value.replace(/\s+/g, ' ');
        });
        Joomla.submitform(task, form);
    }
};

window.onload = function () {
    getDirector();
};

function getDirector() {
    let section = document.querySelector("#jform_sectionID");
    let val = jQuery(section).val();
    if (val < 1) return false;
    fetch(`index.php?option=com_pp&task=section.execute&id=${val}&format=json`)
        .then(function (response) {
            return response.json();
        })
        .then(function (text) {
            let managerID = text.data.managerID;
            let field = document.querySelector("#jform_directorID");
            jQuery(field).val(managerID).trigger("liszt:updated");
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });
    console.log();
}

