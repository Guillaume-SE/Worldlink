document.addEventListener("DOMContentLoaded", function () {
  //set une destination dans Départ et/ou Arrivée
  function chooseDestinationWithClick() {
    const departureButtons = document.querySelectorAll("#button_departure");
    const arrivalButtons = document.querySelectorAll("#button_arrival");

    for (
      let i = 0; i < departureButtons.length; i++ )//pour chaque boutons on ajoute l'évent
    {
      departureButtons[i].addEventListener("click", defineDeparture);
    }
    for (let i = 0; i < arrivalButtons.length; i++) {
      arrivalButtons[i].addEventListener("click", defineArrival);
    }
  }
  //set valeur des boutons départ dans les input select
  function defineDeparture() {
    const selectDeparture = document.querySelector("#departure");
    const idcity = this.dataset.idCity; //data-id-city des boutons homeDestination

    for (let i = 0; i < selectDeparture.options.length; i++) {
      if (selectDeparture.options[i].value == idcity) {
        // si value d'une option du select est = au bouton cliqué
        selectDeparture.options[i].selected = true;
      }
    }
  }
  //set valeur des boutons arrivée dans les input select
  function defineArrival() {
    const selectArrival = document.querySelector("#arrival");
    const idcity = this.dataset.idCity;

    for (let i = 0; i < selectArrival.options.length; i++) {
      if (selectArrival.options[i].value == idcity) {
        selectArrival.options[i].selected = true;
      }
    }
  }
  
  // change couleur navbar selon la route
  const pageChanger = {
   getNavbarText : function(){
      return document.querySelectorAll(".navbar_text--red");
   },
   getNavbarIcons : function(){
     return document.querySelectorAll(".navbar_icon--red");
   },
   getCurrentRoute : function(){
     const currentUrl = new URLSearchParams(document.location.search);
     
     return currentUrl.get("route");
   },
   route : {
      "userAccount": { "children" : [ "informationsUser" , "bookingsUser", "detailsBookingUser", "formCancelBooking" ] },
      "homeDestinations": { "children" : [ "detailsItineraryUsers" , "detailsItineraryTickets" ] },
      "homeServices": { "children" : [] },
      "homeHelp": { "children" : [] },
   },
   changeNavTextColor: function(){
      const navbarText = this.getNavbarText();
      const currentRoute = this.getCurrentRoute();
      const route = this.route;
      for( let i = 0; i < navbarText.length; i++ ) {
        if( currentRoute ==  navbarText[i].dataset.route || route[navbarText[i].dataset.route] && route[navbarText[i].dataset.route].children.includes(currentRoute) ) {
          navbarText[i].classList.add("header__navbar__text--red");
        }
    	}
   	},
    changeNavIconsColor: function(){
      const navbarIcons = this.getNavbarIcons();
      const currentRoute = this.getCurrentRoute();
      const route = this.route;
   	  for( let i = 0; i < navbarIcons.length; i++ ) {
        if( currentRoute == navbarIcons[i].dataset.route || route[navbarIcons[i].dataset.route].children.includes(currentRoute) ) {
          navbarIcons[i].classList.add("header__navbar__icon--red");
        }
    	}
    }
   };
  // ajout event btn swap form destinations
  const btnSwapFormActions = {
    getBtnSwap : function() {
        return document.querySelector("#swap_destinations");
    },
    getBtnSwapImg : function() {
        return document.querySelector("#btnSwap_img");
    },
    getDepartureSelect : function() {
        return document.querySelector("#departure");
    },
    getArrivalSelect : function() {
        return document.querySelector("#arrival");
    },
    addEventBtnSwap : function() {
        const btnSwap = this.getBtnSwap();
        if( btnSwap !== null ) {
          btnSwap.addEventListener("click", () => {
            this.rotateBtnImage();
            this.swapDepartureWithArrival();
        });
      }
    },
    rotateBtnImage : function() {
        const btnImage = this.getBtnSwapImg();
        if( btnImage !== null ) {
         if (btnImage.className === "pickDestination__btn--img") {
             btnImage.className = "pickDestination__btn--img_alt";
         } 
         else if (btnImage.className === "pickDestination__btn--img_alt") {
                  btnImage.className = "pickDestination__btn--img";
        }
      }
    },
    swapDepartureWithArrival : function() {
        const departure = this.getDepartureSelect();
        const arrival = this.getArrivalSelect();
        
        if( departure !== null && arrival !== null) {
          let tempo = departure.value;
          departure.value = arrival.value;
          arrival.value = tempo;
        }
      }
    };
  
  //Ajax de homeDestination
  function destinationsMatchWithResearch(inputHomeDestiValue) {
    fetch("index.php?route=filterDestinations&search=" + inputHomeDestiValue)
      .then((response) => response.json())
      .then((results) => {
        const target = document.querySelector("#target");
        target.innerHTML = "";
        for (let i = 0; i < results.length; i++) {
          let article = document.createElement("article");
          article.classList.add("destinationCard__card");
          article.innerHTML = `
                <div>
                  <img
                    class="destinationCard__img"
                    src="${results[i][6]}"
                    alt="${results[i][7]}"
                  />
                </div>
                
                <div class="destinationCard__infos">
                  <h2>${results[i][2]}</h2>
                  <p class="destinationCard__country">
                    ${results[i][1]}
                  </p>
                  <p class="destinationCard__worldarea">
                    ${results[i][3]}
                  </p>
                  <p class="destinationCard__price">
                    dès
                    ${results[i][5]}
                    €
                  </p>
                  
                  <div class="destinationCard__btn_block">
                  <!--data-id-city et data-departure pour le JS-->
                    <button
                      type="button"
                      class="destinationCard__btn--departure destinationCard__btn--zoom"
                      id="button_departure"
                      data-id-city="${results[i][0]}"
                      data-departure="${results[i][2]} (${results[i][1]})"
                    >
                      Départ
                    </button>
                    
                    <button
                      type="button"
                      class="destinationCard__btn--arrival destinationCard__btn--zoom"
                      id="button_arrival"
                      data-id-city="${results[i][0]}"
                      data-arrival="${results[i][2]} (${results[i][1]})"
                    >
                      Arrivée
                    </button>
                  </div>
                </div>
              `;
          target.append(article);
        }
        chooseDestinationWithClick();
      });
  }

  //condition afficher edit.svg pannel users
  function getIconEdit(isSuperAdmin, results, i) {
    if (isSuperAdmin) {
      return `<p>
                <a href="index.php?route=formEditUsersRole&userid=${results[i][0]}" 
                   title="Modifier le rôle">
                  <img src="public/img/edit.svg" alt="modifier">
                 </a>
              </p>`;
    }
    return "";
  }
  // condition afficher delete.svg pannel users
  function getIconDelete(isSuperAdmin, isAdmin, results, i) {
    if (isSuperAdmin || (isAdmin && results[i].role === "user")) {
      return `<p>
                <a href="index.php?route=formDeleteUsers&userid=${results[i][0]}" 
                   title="Supprimer le compte">
                  <img src="public/img/delete.svg" alt="supprimer">
                  </a>
                </p>`;
    }
    return `<p>
              <img src="public/img/delete-unavailable.svg" alt="supprimer">
            </p>`;
  }

  //Ajax du backoffice Users
  function recupListUsers(inputUsersValue) {
    fetch("index.php?route=searchUsers&search=" + inputUsersValue)
      .then((response) => response.json())
      .then((data) => {
        const { isAdmin, isSuperAdmin, results } = data;
        const target = document.querySelector("#target");
        target.innerHTML = "";

        for (let i = 0; i < results.length; i++) {
          const tr = document.createElement("tr");
          const iconEdit = getIconEdit(isSuperAdmin, results, i);
          const iconDelete = getIconDelete(isSuperAdmin, isAdmin, results, i);
          let trContent = `
             <tr>
                <td>
                  <p>
                    <a href="index.php?route=detailsUsers&userid=${results[i][0]}" 
                       title="Détails">${results[i][0]}</a>
                  </p>
                </td>
                <td>
                  ${results[i][2]}
                </td>
                <td>
                  ${results[i][1]}
                </td>
                <td>
                  ${results[i][3]} 
                </td>
                <td>
                  ${results[i][5]} 
                </td>
                <td>
                  ${results[i][6]} 
                </td>
                <td>
                  ${iconEdit}
                </td>
                <td> 
                  ${iconDelete}
                </td>
            </tr>`;
          tr.innerHTML = trContent;
          target.append(tr);
        }
      });
  }
  // pour icone disponibilité pannelDestinations
  function getIconCheckedOrCrossed(results, i) {
    if (results[i][9] === 1) {
      return `<p>
                  <img src="public/img/check.svg" alt="oui">
                </p>
                `;
    }
    return `<p>
                <img src="public/img/canceled.svg" alt="non">
              </p>
              `;
  }
  //Ajax pannel destinations
  function recupListDestinations(inputDestiValue) {
    fetch("index.php?route=searchDestinations&search=" + inputDestiValue)
      .then((response) => response.json())
      .then((results) => {
        const target = document.querySelector("#target");
        target.innerHTML = "";
        for (let i = 0; i < results.length; i++) {
          const tr = document.createElement("tr");
          const iconCheckedOrCrossed = getIconCheckedOrCrossed(results, i);
          let trContent = `
                <tr>
                  <td>
                    ${results[i][0]}
                  </td>
                  <td>
                    ${results[i][1]}
                  </td>
                  <td>
                    ${results[i][2]}
                  </td>
                  <td>
                    ${results[i][3]}
                  </td>
                  <td>
                    ${results[i][4]}
                  </td>
                  <td>
                    ${results[i][5]}
                  </td>
                  <td>
                    ${results[i][7]}
                  </td>
                  <td>
                    ${results[i][8]}
                  </td>
                  <td>
                    <a href="index.php?route=detailsPictures&pictureid=${results[i][6]}" 
                       title="Détails photo">${results[i][6]}</a>
                  </td>
                  <td>
                    ${iconCheckedOrCrossed}
                  </td>
                  <td>
                    <p>
                      <a href="index.php?route=formEditDestinations&destinationid=${results[i][0]}&pictureid=${results[i][6]}"
                         title="Modification"><img src="public/img/edit.svg" alt="modifier"></a>
                    </p>
                  </td>
                  <td>
                    <p>
                      <a href="index.php?route=deleteDestinations&destinationid=${results[i][0]}&pictureid=${results[i][6]}" 
                         title="Suppression"><img src="public/img/delete.svg" alt="supprimer"></a>
                    </p>
                  </td>
                </tr>`;
          tr.innerHTML = trContent;
          target.append(tr);
        }
      });
  }

  // pour icone statut resa pannel bookings
  function getIconCheckedOrRefunded(results, i) {
    if (results[i][6] === "Confirmée") {
      return `<p>
                  <img src="public/img/check.svg" alt="confirmée" />
                </p>
                `;
    }
    return `<p>
                <img src="public/img/refund.svg" alt="remboursée">
              </p>
              `;
  }
  // pour icone user pannel bookings
  function getIconDeleted(results, i) {
    if (results[i][1] === null) {
      return `<p>
                  <img src="public/img/deleted-user.svg" alt="supprimé">
                </p>
                `;
    }
    return results[i][1];
  }
  //Ajax pannel bookings
  function recupListBookings(inputBookingsValue) {
    fetch("index.php?route=searchBookings&search=" + inputBookingsValue)
      .then((response) => response.json())
      .then((results) => {
        const target = document.querySelector("#target");
        target.innerHTML = "";

        for (let i = 0; i < results.length; i++) {
          const iconCheckedOrRefunded = getIconCheckedOrRefunded(results, i);
          const iconDeleted = getIconDeleted(results, i);
          let tr = document.createElement("tr");
          let trContent = `
                <tr>
              <td>
                ${results[i][0]}
              </td>
              <td>
                ${iconDeleted}
              </td>
              <td>
                ${results[i][2]}
              </td>
              <td>
                ${results[i][3]}
              </td>
              <td>
                ${results[i][4]}
              </td>
              <td>
                ${results[i][5]}
              </td>
              <td>
                ${results[i][7]}
              </td>
              <td>
                ${iconCheckedOrRefunded}
              </td>
            </tr>
                `;
          tr.innerHTML = trContent;
          target.append(tr);
        }
      });
  }

  //Ajax listing users
  const inputUsers = document.querySelector("#search_users");
  if (inputUsers !== null) {
    inputUsers.addEventListener("input", () => {
      recupListUsers(inputUsers.value); //inputUsers.value donne une valeur à inputUsersValue de la fonction recupListUsers
    });
  }

  //Ajax listing destinations
  const inputDesti = document.querySelector("#search_destinations");
  if (inputDesti !== null) {
    inputDesti.addEventListener("input", () => {
      recupListDestinations(inputDesti.value);
    });
  }

  //Ajax listing réservations
  const inputBookings = document.querySelector("#search_bookings");
  if (inputBookings !== null) {
    inputBookings.addEventListener("input", () => {
      recupListBookings(inputBookings.value);
    });
  }

  //Ajax des destinations dans homeDestinations
  const inputHomeDesti = document.querySelector("#filter_destinations");
  if (inputHomeDesti !== null) {
    inputHomeDesti.addEventListener("input", () => {
      destinationsMatchWithResearch(inputHomeDesti.value);
    });
  }
  // annule le submit de la touche ENTER sur les champs input simple
  const preventSubmitOnEnter = document.querySelector(".prevent_enter");
  if (preventSubmitOnEnter !== null) {
    preventSubmitOnEnter.addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
      }
    });
  }
  // ferme le clavier sur mobile
  const closeKeyboardOnMobile = document.querySelector(".close_keyboard");
  if (closeKeyboardOnMobile !== null) {
    closeKeyboardOnMobile.addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        closeKeyboardOnMobile.blur();
      }
    });
  }
  btnSwapFormActions.addEventBtnSwap();
  btnSwapFormActions.rotateBtnImage();
  btnSwapFormActions.swapDepartureWithArrival();
  pageChanger.changeNavTextColor();
  pageChanger.changeNavIconsColor();
  chooseDestinationWithClick();
});