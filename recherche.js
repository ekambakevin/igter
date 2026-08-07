
document.addEventListener("DOMContentLoaded", function () {

    // =====================================================
    // RÉCUPÉRER LA RECHERCHE
    // =====================================================

    const params = new URLSearchParams(window.location.search);
    const recherche = params.get("q");

    if (!recherche) {
        return;
    }

    // =====================================================
    // NORMALISER LE TEXTE
    // =====================================================

    const terme = recherche
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();

    // =====================================================
    // DÉFINIR LES DESTINATIONS
    // =====================================================

    const destinations = [

        // =============================================
        // PAGE INDEX.HTML
        // =============================================

        {
            mots: [
                "attribution",
                "attributions"
            ],
            page: "index.html",
            cible: "#attributions"
        },

        {
            mots: [
                "actualite",
                "actualites",
                "news"
            ],
            page: "index.html",
            cible: "#actualite"
        },

        {
            mots: [
                "statistique",
                "statistiques",
                "stat",
                "stats"
            ],
            page: "index.html",
            cible: "#statistique"
        },

        {
            mots: [
                "contact",
                "adresse",
                "telephone",
                "email"
            ],
            page: "index.html",
            cible: "#contact"
        },

        // ==================================================
        // PAGES APERCU GENERAL.HTML 
        // ==================================================

        {
            mots: [
                "histoire",
                "historique",
                "apercu general",
                "apercu"
            ],
            page: "apercu general.html",
            cible: "#apercu"
        },

        // ==================================================
        // PAGES DOCUMENT, MISSION ET MODE DE SAISINE
        // ==================================================

        {
            mots: [
                "document",
                "decret",
                "organigramme",
                "communique",
                "note",
                "note de service",
                "note circulaire",
                "message officiel",
                "message",
                "decision",
                "rapport"
            ],
            page: "document.html",
            cible: "#documents"
        },

        {
            mots: [
                "mission",
                "mision",
                "missions"
            ],
            page: "mission.html",
            cible: "#mission"
        },

        {
            mots: [
                "vous saisir",
                "formulaire de saisine",
                "saisir",
                "formulaire",
                "mode de saisine",
                "saisine",
                "vous ecrire",
                "ecrire"
            ],
            page: "mode_saisine.html",
            cible: "#saisine"
        },

        // =============================================
        // PAGES DES DIRIGEANTS IGTER
        // =============================================

        {
            mots: [
                "ig",
                "iga",
                "i.g",
                "i.g.a",
                "inspeceteur general",
                "inspeceteur general adjoint"
            ],

            page: "ig_iga.html",
            cible: "#ig_iga"
        },

        {
            mots: [
                "directeur",
                "directeurs",
                "direction standard",
                "direction metier"
            ],
            page: "directeurs.html",
            cible: "#directeurs"
        },

        {
            mots: [
                "chef de pool",
                "chef pool",
                "inspool"
            ],
            page: "directeurs2.html",
            cible: "#directeurs2"
        },

        {
            mots: [
                "inspecteur provincial",
                "inspro"
            ],
            page: "directeurs3.html",
            cible: "#directeurs3"
        },

        {
            mots: [
                "expert inspecteur",
                "expert-inspecteur",
                "expert"
            ],
            page: "directeurs4.html",
            cible: "#directeurs4"
        },


        {
            mots: [
                "cd",
                "chef de division",
                "chef division"
            ],
            page: "cd.html",
            cible: "#cd"
        },

         {
            mots: [
                "cb",
                "chef de bureau",
                "chef bureau"
            ],
            page: "cb.html",
            cible: "#cb"
        },
        
        // =============================================
        // PROVINCES, ETDS ET SERVICES CENTRAUX DU MINISTERE DE L'INTERIEUR
        // =============================================

        {
            mots: [
                "province",
                "province de la RDC"
            ],
            page: "province.html",
            cible: "#province"
        },

        {
            mots: [
                "entite territoriale",
                "entite",
                "etd"
            ],
            page: "etd1.html",
            cible: "#etd1"
        },

        {
            mots: [
                "ministère de l'Intérieur",
                "interieur",
                "service Intérieur"
            ],
            page: "scdmisdac.html",
            cible: "#scdmisdac"
        },

        // =============================================
        // GALERIE DES PHOTOS MISSIONS REALISEES
        // =============================================

        {
            mots: [
                "galerie",
                "photo",
                "galerie photo",
                "galerie des photos",
                "image"
            ],
            page: "galerie.html",
            cible: "#galerie"
        },

        // =============================================
        // STRUCTURES ORGANIQUES
        // =============================================

        {
            mots: [
                "administration centrale",
                "centrale",
                "administration",
                "services",
                "service"
            ],
            page: "administration_centrale.html",
            cible: "#administration"
        },

        {
            mots: [
                "pool"
            ],
            page: "pool.html",
            cible: "#pool"
        },

        {
            mots: [
                "antenne territoriale",
                "antenne",
                "territoriale",
                "territoire",
                "inspection",
                "provinciale",
                "inspection provinciale",
                "inspections provinciales"
            ],
            page: "inspection_provinciale.html",
            cible: "#ipter"
        }

    ];


    // =====================================================
    // RECHERCHER UNE CORRESPONDANCE
    // =====================================================

    const destination = destinations.find(item => {

        return item.mots.some(mot => terme.includes(mot));

    });


    // =====================================================
    // AUCUN RÉSULTAT
    // =====================================================

    if (!destination) {

        alert(
            'Aucun résultat trouvé pour : "' +
            recherche +
            '"\nVeuillez bien saisir votre mot clé.'
            );

        // Supprimer ?q=... de l'URL
        window.history.replaceState({}, document.title, window.location.pathname);

    return;
    }

    // =====================================================
    // DÉTERMINER LA PAGE ACTUELLE
    // =====================================================

    const pageActuelle = window.location.pathname
        .split("/")
        .pop();


    // =====================================================
    // SI LE RÉSULTAT EST SUR LA PAGE ACTUELLE
    // =====================================================

    if (
        pageActuelle === destination.page &&
        destination.cible !== ""
    ) {

        const element = document.querySelector(
            destination.cible
        );

        if (element) {

            setTimeout(function () {

            element.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

        // Supprimer ?q=... de l'URL
        window.history.replaceState(
            {},
            document.title,
            window.location.pathname + destination.cible
        );

    }, 100);

}

    return;

    }


    // =====================================================
    // SI LE RÉSULTAT EST SUR UNE AUTRE PAGE
    // =====================================================

    let url = destination.page;

    if (destination.cible !== "") {

        url += destination.cible;

    }

    window.location.href = url;
    window.history.replaceState({}, document.title, nouvelleURL);

});
