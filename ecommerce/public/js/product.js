
$('#add-image').click(function(){
    // Récupérer le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    // Récupérer le prototype des entrées
    const tmpl = $('#product_images').data('prototype').replace(/__name__/g, index);

    // J'injecte ce code au sein de la div
    $('#product_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons()
{
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter()
{
    const count = +$('#product_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButtons();