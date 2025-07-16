const gallySearchFormHandler = function (event) {
    const gallySearchFormContainer = document.querySelector('#searchFormContainer');
    if (null !== gallySearchFormContainer) {
        const gallyPreviewUrl = gallySearchFormContainer.dataset.previewUrl;
        const gallySearchForm = gallySearchFormContainer.querySelector('form');
        const gallySearchInput = gallySearchForm.querySelector('input');
        const gallySearchResult = gallySearchFormContainer.querySelector('#collapsedSearchResults');

        let abortController = null;
        const gallySearchResultCollapsible = bootstrap.Collapse.getOrCreateInstance(gallySearchResult, {
            toggle: false
        });

        gallySearchInput.addEventListener('input', (event) => {
            const queryText = event.target.value;

            if (queryText.length >= 3) {
                let formData = new FormData(gallySearchForm);
                const plainFormData = Object.fromEntries(formData.entries());

                let formDataArray = [];
                for (const [key, value] of Object.entries(plainFormData)) {
                    formDataArray.push(`${encodeURIComponent(key)}=${value}`);
                }
                let formDataString = formDataArray.join('&');

                gallySearchResult.querySelector('.loading-results').classList.remove('d-none')
                gallySearchResult.querySelector('.results').classList.add('d-none')
                gallySearchResult.querySelector('.results').textContent = '';
                gallySearchResultCollapsible.show();

                if (null !== abortController) {
                    abortController.abort();
                    abortController = null;
                }

                abortController = new AbortController();

                (async () => {
                    const rawResponse = await fetch(gallyPreviewUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: formDataString,
                        signal: abortController.signal
                    }).catch((error) => {
                        // If the request was aborted, do nothing
                        if (error.name === 'AbortError') {
                            return;
                        }

                        // Otherwise, handle the error here or throw it back to the console
                        throw error;
                    });

                    // On aborted calls, rawResponse will be undefined
                    if (rawResponse !== undefined) {
                        const content = await rawResponse.json();
                        // Do something with the results here

                        gallySearchResult.querySelector('.loading-results').classList.add('d-none');
                        gallySearchResult.querySelector('.results').classList.remove('d-none');
                        gallySearchResult.querySelector('.results').innerHTML = content.htmlResults;
                    }
                })();
            } else {
                gallySearchResultCollapsible.hide();
            }
        });

        //
        gallySearchInput.addEventListener('focus', (event) => {
            const queryText = event.target.value;
            if (queryText.length >= 3) {
                // If there are already some results, display them
                if (gallySearchResult.querySelector('.loading-results').classList.contains('d-none')) {
                    gallySearchResultCollapsible.show();
                }
                // Otherwise, trigger a search
                else {
                    gallySearchInput.dispatchEvent(new Event('input'));
                }
            }
        });

        // Add a listener to close the searchResult container
        document.addEventListener('click', function (event) {
            const gallySearchResult = event.target.closest('#collapsedSearchResults');
            if (null === gallySearchResult) {
                gallySearchResultCollapsible.hide();
            }
        });
    }
}

window.addEventListener("DOMContentLoaded", gallySearchFormHandler);
