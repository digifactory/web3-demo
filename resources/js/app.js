import WalletConnect from "@walletconnect/client";
import QRCodeModal from "@walletconnect/qrcode-modal";

window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

function toHex(s) {
    // utf8 to latin1
    var s = decodeURI(encodeURIComponent(s))
    var h = ''
    for (var i = 0; i < s.length; i++) {
        h += s.charCodeAt(i).toString(16)
    }
    return h
}

(() => {
    // Create a connector
    const connector = new WalletConnect({
        bridge: "https://bridge.walletconnect.org", // Required
        qrcodeModal: QRCodeModal,
    });

    // Subscribe to connect event
    connector.on("connect", (error, payload) => {
        if (error) {
            throw error;
        }

        login();
    });

    function startSession() {
        // Check if connection is already established, then prompt user to login (sign message)
        if (connector.connected) {
            login();
        } else {
            connector.createSession();
        }
    }

    function showLoginScreen() {
        document.getElementById('login-screen').classList.remove('d-none');
    }

    function hideLoginScreen() {
        document.getElementById('login-screen').classList.add('d-none');
    }

    function showSignPromptScreen() {
        document.getElementById('sign-prompt-screen').classList.remove('d-none');
    }

    function hideSignPromptScreen() {
        document.getElementById('sign-prompt-screen').classList.add('d-none');
    }

    function showScanQrScreen() {
        document.getElementById('scan-qr-screen').classList.remove('d-none');
    }

    function hideScanQrScreen() {
        document.getElementById('scan-qr-screen').classList.add('d-none');
    }

    function showSignFailedScreen() {
        document.getElementById('sign-failed-screen').classList.remove('d-none');
    }

    function hideSignFailedScreen() {
        document.getElementById('sign-failed-screen').classList.add('d-none');
    }

    function login() {
        if (! window.guest) {
            return;
        }

        axios
            .get('/auth/nonce')
            .then((result) => {
                if (result.status === 200) {
                    const address = connector.accounts[0];

                    showSignPromptScreen();
                    hideLoginScreen();
                    hideSignFailedScreen();

                    connector
                        .signPersonalMessage([
                            '0x'+toHex(result.data.nonce),
                            address,
                        ])
                        .then((signature) => {
                            axios
                                .post('/auth/verify', {
                                    address,
                                    signature,
                                })
                                .then((result) => {
                                    if (result.status === 200) {
                                        window.location.reload();
                                    }
                                });
                        })
                        .catch((error) => {
                            hideSignPromptScreen();
                            showSignFailedScreen();
                        });
                }
            });
    }

    window.walletConnectLogin = {
        startSession,
    };
})();