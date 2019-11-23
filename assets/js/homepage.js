import '../css/homepage.scss';
import { renderSigninButton, handleAuthClick } from './gmail_client';

function homepageCallback(html) {
    document.body.classList.add('homepage-body');
    document.getElementsByClassName('container')[0].innerHTML = html;
    document.getElementById('btn-google-authorize').onclick = handleAuthClick;
    renderSigninButton();
}

export { homepageCallback };
