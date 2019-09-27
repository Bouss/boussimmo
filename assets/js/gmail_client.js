// Client ID and API key from the Developer Console
const API_KEY = 'AIzaSyCh6j2cA9t__fPfuVz46uXjt5sr527J2hE';
const CLIENT_ID = '671957420201-v4cld6vadigi64cpo25hcssv53sptvtl.apps.googleusercontent.com';

// Array of API discovery doc URLs for APIs used by the quickstart
const DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/gmail/v1/rest"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
const SCOPES = 'https://www.googleapis.com/auth/gmail.readonly';

let authorizeButton = document.getElementById('btn-google-authorize');
let signoutButton = document.getElementById('btn-google-signout');

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
        authorizeButton.onclick = handleAuthClick;
        signoutButton.onclick = handleSignoutClick;
    }, function(error) {
        appendPre(JSON.stringify(error, null, 2));
    });
}

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        authorizeButton.style.display = 'none';
        signoutButton.style.display = 'block';
        fillPropertyAdContainer();
    } else {
        authorizeButton.style.display = 'block';
        signoutButton.style.display = 'none';
    }
}

/**
 *  Sign in the user upon button click.
 */
function handleAuthClick(event) {
    gapi.auth2.getAuthInstance().signIn();
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
 * Print all Labels in the authorized user's inbox. If no labels
 * are found an appropriate message is printed.
 */
function listLabels() {

    gapi.client.gmail.users.labels.list({
        'userId': 'me'
    }).then(function(response) {
        let labels = response.result.labels;
        appendPre('Labels:');

        if (labels && labels.length > 0) {
            for (i = 0; i < labels.length; i++) {
                let label = labels[i];
                appendPre(label.name)
            }
        } else {
            appendPre('No Labels found.');
        }
    });
}

function fillPropertyAdContainer() {
    let propertyAdContainer = document.getElementById('property-ad-container');

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list'),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        contentType: 'application/octet-stream; charset=utf-8',
        processData: false,
        data: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token
    }).done(function (data) {
        propertyAdContainer.innerHTML = data;
    });
}
