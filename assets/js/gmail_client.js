import { homepageCallback } from './homepage';
import { propertyAdIndexCallback } from './property_ad_index';

// Client ID and API key from the Developer Console
const API_KEY = 'AIzaSyCh6j2cA9t__fPfuVz46uXjt5sr527J2hE';
const CLIENT_ID = '671957420201-v4cld6vadigi64cpo25hcssv53sptvtl.apps.googleusercontent.com';

// Array of API discovery doc URLs for APIs used by the quickstart
const DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/gmail/v1/rest"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
const SCOPES = 'https://www.googleapis.com/auth/gmail.readonly';

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

function renderSigninButton() {
    gapi.signin2.render('btn-google-authorize', {
        width: 240,
        height: 50,
        longtitle: true,
        theme: 'dark',
    });
}

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        signIn();
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
            console.log('Authenticated');
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
        homepageCallback(html);
    });
}

function signIn() {
    $.ajax({
        type: 'POST',
        url: Routing.generate('log_in'),
        data: {
            access_token: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
            email: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getEmail(),
            profile_image: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getImageUrl(),
        }
    }).done(function (data) {
        console.log(data);
        loadPropertyAdIndex();
    });
}

function loadPropertyAdIndex() {
    getLabels(function (labels) {
        $.ajax({
            type: 'GET',
            url: Routing.generate('property_ad_index'),
            data: {
                profile_image: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getImageUrl(),
                email: gapi.auth2.getAuthInstance().currentUser.get().getBasicProfile().getEmail(),
                labels: labels
            }
        }).done(function (html) {
            propertyAdIndexCallback(html);
        });
    });
}

export { renderSigninButton, handleAuthClick, handleSignoutClick }
