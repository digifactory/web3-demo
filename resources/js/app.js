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

    // Check if connection is already established, then prompt user to login (sign message)
    if (connector.connected) {
        login();
    }

    // Subscribe to connect event
    connector.on("connect", (error, payload) => {
        if (error) {
            throw error;
        }

        login();
    });

    function startSession() {
        if (!connector.connected) {
            connector.createSession();
        }
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
                                    console.log(result);
                                });
                        })
                        .catch((error) => {
                            // Error returned when rejected
                            console.error(error);
                        });
                }
            });
    }

    window.walletConnectLogin = {
        startSession,
    };
})();