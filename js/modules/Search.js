import $ from "jquery";

class Search {
  // 1. Describe and create/initiate our object
  constructor() {
    this.addSearchHTML();
    // El DOM es muy lento mejor guardar en variables para acceder luego a los elementos HTML
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.resultsOverlay = $("#search-overlay__results");
    this.isOverlayOpen = false;
    this.typingTimer;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.events();
  }

  // 2. Events
  events() {
    // El método on hace que el metodo que se pasa como segundo argumento se ejecute sobre el elemento en el que se dispara el evento. Por lo que para setearlo sobre la instancia de la clase hay que pasarle al evento el bind(this)
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));

    // Para escuchar teclas s y esc para mismas funciones de abrir y cerrar
    $(document).on("keydown", this.keyPressDispatcher.bind(this));

    // Escritura en el inpup. Usamos key up para que el browser tenga algo de tiempo entre la escritura para actualizar this.searchField.val() con el contenido actual del input. Si se usa keydown this.searchField.val() tiene siempre una letra menos de la que tiene el input al dispararse el evento
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  // 3. methods (function, action ...)

  getResults() {
    // Para buscar contenido en la bbdd de wp apuntamos a localhost/wordpress/wp-json/wp/v2/posts y hacemos un get request de un termino con ?search=busqueda. Esto busca en los posts no en los custom posts
    // Vamos a usar `` para meter dentro html (template literal)
    // El metodo map crea un nuevo array con cada uno de los elemento del array que llama a este metodo, usamos join al final para unir los elementos del nuevo array creado separados por "" (nada) (si no se imprimirian como a,b,c)
    // themeData.root_url apunta a la url del sitio web, se crea en functions.php

    // Dentro de when se realiza todo asincronamente y then cuando todas los hilos han terminado. Los request devuelven los resultados en las variables dentro de then, pero ademas del JSON llevan informacion sobre el request por lo que tenemos que coger el elemento 0 del array que es el respuesta de wp a la peticion
    // En el then ejecutamos como primer argumento (exito) una función con las respuestas de las peticiones y como segundo parámetro (error en la peticion) una función para avisar de un error ocurrido
    // $.when(
    //   $.getJSON(
    //     themeData.root_url +
    //       "/wp-json/wp/v2/posts?search=" +
    //       this.searchField.val()
    //   ),
    //   $.getJSON(
    //     themeData.root_url +
    //       "/wp-json/wp/v2/pages?search=" +
    //       this.searchField.val()
    //   )
    // ).then(
    //   (posts, pages) => {
    //     var combinedResults = posts[0].concat(pages[0]);
    //     this.resultsOverlay.html(`
    //       <h2 class="search-overlay__section-item">
    //         General Information
    //       </h2>
    //       ${
    //         combinedResults.length
    //           ? '<ul class="link-list min-list">'
    //           : "<p>The search matched no general information</p>"
    //       }
    //       ${combinedResults
    //         .map(
    //           result =>
    //             `<li><a href="${result.link}">${result.title.rendered}</a>${
    //               result.type == "post"
    //                 ? " post created by " + result.authorName
    //                 : ""
    //             }</li>`
    //         )
    //         .join("")}
    //         ${combinedResults.length ? "</ul>" : ""}
    //   `);
    //     this.isSpinnerVisible = false;
    //   },
    //   () => {
    //     this.resultsOverlay.html(`<p>Unexpected error, try again</p>`);
    //   }
    // );

    $.getJSON(
      themeData.root_url +
        "/wp-json/ns_my_theme/v1/search?term=" +
        this.searchField.val(),
      results => {
        this.resultsOverlay.html(`
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-item">
              General Information
            </h2>
            ${
              results.generalInfo.length
                ? '<ul class="link-list min-list">'
                : "<p>The search matched no general information</p>"
            }
            ${results.generalInfo
              .map(
                result =>
                  `<li><a href="${result.link}">${result.title}</a>${
                    result.postType == "post"
                      ? " post created by " + result.authorName
                      : ""
                  }</li>`
              )
              .join("")}
            ${results.generalInfo.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-item">
              Programs
            </h2>
            ${
              results.programs.length
                ? '<ul class="link-list min-list">'
                : `<p>The search matched no programs information. <a href="${themeData.root_url}/programs">View all programs</a></p>`
            }
            ${results.programs
              .map(
                result =>
                  `<li><a href="${result.link}">${result.title}</a></li>`
              )
              .join("")}
            ${results.programs.length ? "</ul>" : ""}
            <h2 class="search-overlay__section-item">
              Professors
            </h2>
            ${
              results.professors.length
                ? '<ul class="link-list min-list">'
                : "<p>The search matched no professors information</p>"
            }
            ${results.professors
              .map(
                result =>
                  `<li><a href="${result.link}">${result.title}</a></li>`
              )
              .join("")}
            ${results.professors.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-item">
              Campuses
            </h2>
            ${
              results.campuses.length
                ? '<ul class="link-list min-list">'
                : `<p>The search matched no campuses information. <a href="${themeData.root_url}/campuses">View all campuses</a></p>`
            }
            ${results.campuses
              .map(
                result =>
                  `<li><a href="${result.link}">${result.title}</a></li>`
              )
              .join("")}
            ${results.campuses.length ? "</ul>" : ""}
            <h2 class="search-overlay__section-item">
              Events
            </h2>
            ${
              results.events.length
                ? '<ul class="link-list min-list">'
                : `<p>The search matched no events information. <a href="${themeData.root_url}/events">View all events</a></p>`
            }
            ${results.events
              .map(
                result =>
                  `<li><a href="${result.link}">${result.title}</a></li>`
              )
              .join("")}
            ${results.events.length ? "</ul>" : ""}
          </div>
        </div>
          `);
        this.isSpinnerVisible = false;
      }
    );

    // Sincrona, cuando termina un request se hace el siguiente
    // $.getJSON(
    //   themeData.root_url +
    //     "/wp-json/wp/v2/posts?search=" +
    //     this.searchField.val(),
    //   posts => {
    //     $.getJSON(
    //       themeData.root_url +
    //         "/wp-json/wp/v2/pages?search=" +
    //         this.searchField.val(),
    //       pages => {
    //         var combinedResults = posts.concat(pages);
    //         this.resultsOverlay.html(`
    //       <h2 class="search-overlay__section-item">
    //         General Information
    //       </h2>
    //       ${
    //         combinedResults.length
    //           ? '<ul class="link-list min-list">'
    //           : "<p>The search matched no general information</p>"
    //       }
    //       ${combinedResults
    //         .map(
    //           result =>
    //             `<li><a href="${result.link}">${result.title.rendered}</a></li>`
    //         )
    //         .join("")}
    //         ${combinedResults.length ? "</ul>" : ""}
    //     `);
    //         this.isSpinnerVisible = false;
    //       }
    //     );
    //   }
    // );
  }

  typingLogic(e) {
    // Solo queremos mostrar resultados si se cambia el texto dentro del elemento input, para que no responda con la creacion del spinner ante flechas del teclado, shift o control,
    if (this.previousValue != this.searchField.val()) {
      // Cada vez que se pulse una tecla se reinicia el timer y luego se vuelve a crear, si no se pulsa en 2 segundos se ejecutara la funcion correctamente
      clearTimeout(this.typingTimer);

      // Vamos a comprobar si se ha borrado todo el texto del input para en ese caso no mostrar ni el spinner ni resultados de la busqueda
      if (this.searchField.val() == "") {
        this.resultsOverlay.html("");
        this.isSpinnerVisible = false;
      } else {
        // si el spinner no es visible lo cargamos en el div
        if (!this.isSpinnerVisible) {
          this.resultsOverlay.html("<div class='spinner-loader'><div>");
          this.isSpinnerVisible = true;
        }
        // Creamos un timer de 1 segundo para lanzar la funcion getResults
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);
      }
    }
    this.previousValue = this.searchField.val();
  }

  keyPressDispatcher(e) {
    // check S key(83) and ESC(27)
    if (
      e.keyCode == 83 &&
      !this.isOverlayOpen &&
      // Para que si tenemos un input o textarea abiertos no hagamos esta funcion y permitamos escribir s sin que se habra el area de busqueda (Si no hay input o textarea con el cursor dentro (:focus))
      !$("input, textarea").is(":focus")
    ) {
      this.openOverlay();
    } else if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  // Metodo que activa la clase en el overlay para que sea visible
  // Además de hacer focus en el input para no tener que clickar
  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    // Eliminar scroll en body
    $("body").addClass("body-no-scroll");
    // Esperamos 301 ms para hacer focus en el input que es lo que tarda la animacion del overlay en cargarse (lo que tarda css en hacer la transicion)
    setTimeout(() => this.searchField.focus(), 301);
    // Limpiamos el input para que este vacio en cada busqueda
    this.searchField.val("");
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  // Metodo para añadir al body el html necesario para crear el overlay de búsqueda
  addSearchHTML() {
    $("body").append(
      `<div class="search-overlay">
      <div class="search-overlay__top">
        <div class="container">
          <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
          <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
          <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        </div>
      </div>
      <div class="container">
        <div id="search-overlay__results">
          
        </div>
      </div>
    </div>`
    );
  }
}

export default Search;
