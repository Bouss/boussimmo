// Client ID and API key from the Developer Console
const API_KEY = 'AIzaSyCh6j2cA9t__fPfuVz46uXjt5sr527J2hE';
const CLIENT_ID = '671957420201-v4cld6vadigi64cpo25hcssv53sptvtl.apps.googleusercontent.com';

// Array of API discovery doc URLs for APIs used by the quickstart
const DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/gmail/v1/rest"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
const SCOPES = 'https://www.googleapis.com/auth/gmail.readonly';

let container = document.getElementsByClassName('container')[0];

/**
 *  On load, called to load the auth2 library and API client library.
 */
window.handleClientLoad = function () {
    gapi.load('client:auth2', initClient);
};

/**
 *  Initializes the API client library and sets up sign-in state
 *  listeners.
 */
function initClient() {
    gapi.client.init({
        apiKey: API_KEY,
        clientId: CLIENT_ID,
        discoveryDocs: DISCOVERY_DOCS,
        scope: SCOPES
    }).then(function () {
        // Listen for sign-in state changes.
        gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

        // Handle the initial sign-in state.
        updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    }, function(error) {
        appendPre(JSON.stringify(error, null, 2));
    });
}

window.renderSigninButton = function () {
    gapi.signin2.render('btn-google-authorize', {
        width: 240,
        height: 50,
        longtitle: true,
        theme: 'dark',
    });
};

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        loadPropertyAdIndex();
    } else {
        loadHomepage();
    }
}

/**
 *  Sign in the user upon button click.
 */
function handleAuthClick(event) {
    gapi.auth2.getAuthInstance().signIn().then(function (response) {
        if (response.getAuthResponse()) {
            getLabels(function (labels) {
                if (labels && labels.length > 0) {
                    showSelectGmailLabelsModal(labels);
                }
            });
        }
    });
}

/**
 *  Sign out the user upon button click.
 */
function handleSignoutClick(event) {
    gapi.auth2.getAuthInstance().signOut();
}

/**
 * Append a pre element to the body containing the given message
 * as its text node. Used to display the results of the API call.
 *
 * @param {string} message Text to be placed in pre element.
 */
function appendPre(message) {
    let pre = document.getElementById('content');
    let textContent = document.createTextNode(message + '\n');
    pre.appendChild(textContent);
}

/**
 * Get all Labels in the authorized user's inbox.
 */
function getLabels(callback) {
    gapi.client.gmail.users.labels.list({
        'userId': 'me'
    }).then(function(response) {
        callback(response.result.labels);
    });
}

function loadHomepage() {
    $.ajax({
        type: 'GET',
        url: Routing.generate('homepage'),
    }).done(function (html) {
        container.innerHTML = html;
        document.getElementById('btn-google-authorize').onclick = handleAuthClick;
        renderSigninButton();
    });
}

function loadPropertyAdIndex() {
    $.ajax({
        type: 'GET',
        url: Routing.generate('property_ad_index'),
        data: {
            profile_image: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getImageUrl(),
            email: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getEmail()
        }
    }).done(function (html) {
        container.innerHTML = html;
        document.getElementById('btn-google-signout').onclick = handleSignoutClick;
        fillPropertyAdContainer();
    });
}

function fillPropertyAdContainer() {
    let labels = getCookie('labels');

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list'),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data: {
            access_token: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
            labels: getCookie('labels') ? getCookie('labels') : []
        }
    }).done(function (data) {
        document.getElementById('property-ad-container').innerHTML = data;
    });
}
