import $ from "jquery";

class MyNotes {
  constructor() {
    // Attributos del DOM
    this.myNotesUl = $("#my-notes");
    this.deleteButton = $(".delete-note");
    this.editButton = $(".edit-note");
    this.updateButton = $(".update-note");

    // Eventos
    this.events();
  }

  events() {
    this.deleteButton.on("click", this.deleteNote.bind(this));
    this.editButton.on("click", this.editNote.bind(this));
    this.updateButton.on("click", this.updateNote.bind(this));
  }

  // Methods

  updateNote(e) {}

  // Metodo para editar nota,
  // 1. elimina el atributo read-only de <input> y <textarea>
  // 2. Crea marcos para que parezca editable
  // 3. Focus en <input>
  // 4. Muestra boton <save>
  editNote(e) {
    var thisNote = $(e.target).parents("li");
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    thisNote.find(".note-title-field").focus();
    this.updateButton.addClass("update-note--visible");

    this.editButton.html("Cancel");
  }

  // Metodo para eliminar notas
  deleteNote(e) {
    // Creamos una variable que apunte al <li> padre del boton que envia la nota (e.target es el span que pulsamos)
    var thisNote = $(e.target).parents("li");
    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", themeData.nonce);
      },
      url: themeData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
      type: "DELETE",

      success: response => {
        // Elimina el <li> con slide up animation
        thisNote.slideUp();
        console.log("success");
        console.log(response);
      },
      error: response => {
        console.log("error");
        console.log(response);
      }
    });
  }
}

export default MyNotes;
