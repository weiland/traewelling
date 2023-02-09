let shareButton = document.getElementById("checkin-share");

if (shareButton) {
    shareButton.addEventListener("click", async (e) => {
        const shareData = {
            text: e.target.dataset.shareText,
            url: e.target.dataset.shareLink,
        };

        try {
            if (!navigator.canShare(shareData)) {
                console.error("Sharing this content is not supported by your browser.");
            }

            await navigator.share(shareData);
        } catch (e) {
            console.error(e);
        }
    });
}
