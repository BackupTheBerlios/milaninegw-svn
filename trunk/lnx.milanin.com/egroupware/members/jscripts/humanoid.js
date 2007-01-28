function addField() {
        var  frm= document.getElementById("milanin_add_comment_form");
        var field = document.createElement('input');
        field.id = "humanoid";
        field.name = "humanoid";
        field.type = "hidden";
        field.value = "nonempty";
        frm.appendChild(field);
        frm.submit();
}  
