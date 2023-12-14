window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});

$(document).on('blur', ".maiusculo", function () {
    $(this).val(function (_, val) {
        return val.toUpperCase();
    });
});
//MASCARA PARA CEP
function cepMascara(cep) {
    if (cep.value.length == 5) {
        cep.value = cep.value + '-' 
    }
}

//MASCARA PARA CPF/CNPJ
$("#REGISTRO").keydown(function(){
    try {
        $("#REGISTRO").unmask();
    } catch (e) {}

    var tamanho = $("#REGISTRO").val().length;

    if(tamanho < 11){
        $("#REGISTRO").mask("999.999.999-99");
    } else {
        $("#REGISTRO").mask("99.999.999/9999-99");
    }

    // ajustando foco
    var elem = this;
    setTimeout(function(){
        // mudo a posição do seletor
        elem.selectionStart = elem.selectionEnd = 10000;
    }, 0);
    // reaplico o valor para mudar o foco
    var currentValue = $(this).val();
    $(this).val('');
    $(this).val(currentValue);
});

//MASCARA PARA TELEFONE/CELULAR <input type="tel" maxlength="15" onkeyup="handlePhone(event)" />
const handlePhone = (event) => {
    let input = event.target
    input.value = phoneMask(input.value)
  }
  
  const phoneMask = (value) => {
    if (!value) return ""
    value = value.replace(/\D/g,'')
    value = value.replace(/(\d{2})(\d)/,"($1) $2")
    value = value.replace(/(\d)(\d{4})$/,"$1-$2")
    return value
  }


  //var timeDisplay = document.getElementById("time");

///function refreshTime() {
  //var dateString = new Date().toLocaleString("pt-BR", {timeZone: "America/Sao_Paulo"});
  //var formattedString = dateString.replace(", ", " - ");
  //timeDisplay.innerHTML = formattedString;
//}

//setInterval(refreshTime, 1000);


