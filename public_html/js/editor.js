window.addEventListener('load', function () {

    const fixtureText = function (domElement) {
        return domElement.hasAttribute('data-fixture');
    }

    const editor = ContentTools.EditorApp.get();
    editor.init('[data-editable], [data-fixture]', 'data-name', fixtureText);

    ContentTools.StylePalette.add([
        new ContentTools.Style('H1', 'h1', ['h1'])
    ]);

    editor.addEventListener('saved', function (ev) {
        // Check that something changed
        if (Object.keys(ev.detail().regions).length === 0) {
            return;
        }
        const originalRegions = ev.detail().regions;

        const regions = Object.keys(originalRegions).reduce((acc, key) => {
            const domElement = document.querySelector(`[data-name="${key}"]`);

            return {
                ... acc,
                [key]: fixtureText(domElement) ? domElement.textContent : originalRegions[key]
            }
        }, {})

        // Set the editor as busy while we save our changes
        this.busy(true);

        // Collect the contents of each region into a FormData instance
        const payload = new FormData();
        for (let name in regions) {
            if (regions.hasOwnProperty(name)) {
                payload.append(name, regions[name]);
            }
        }

        // Send the update content to the server to be saved
        function onStateChange(ev) {
            // Check if the request is finished
            if (ev.target.readyState == 4) {
                editor.busy(false);
                if (ev.target.status == '200') {
                    // Save was successful, notify the user with a flash
                    new ContentTools.FlashUI('ok');
                } else {
                    // Save failed, notify the user with a flash
                    new ContentTools.FlashUI('no');
                }
            }
        };

        const xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', onStateChange);
        xhr.open('POST', '/message/edit');
        xhr.send(payload);
    });


});