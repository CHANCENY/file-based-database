class DashboardBehavior {

    #collection_storage;

    #collections_links;

    #collections_data;

    #modal_listing;

    static init() {

        const collections_links = document.querySelectorAll('.collection-name');
        const collection_storage = document.querySelector('#collections');
        const modals = document.querySelectorAll('.modal');
        const behavior = new DashboardBehavior(collections_links, collection_storage, modals);
    }

    constructor(collections_links, collection_storage, modal_listing) {
       this.#collection_storage = collection_storage;
       this.#collections_links = collections_links;
       this.#modal_listing = modal_listing;

       try{
           this.#collections_data = JSON.parse(this.#collection_storage.textContent);
       }catch (e) {
           console.error(e.message)
       }

       this.#collectionKeysEventListener();
    }

    #collectionKeysEventListener() {
        this.#collections_links.forEach((collection_link)=>{
            collection_link.addEventListener('click',(e)=>{
                e.preventDefault();

                const collection_key = collection_link.textContent;
                const collection_name = collection_link.parentElement.parentElement.querySelector('summary').textContent;

                if(this.#modal_listing) {
                    this.#modal_listing.forEach((modal)=>{
                        if(modal.getAttribute('itemtype') === 'collection-key-modal') {
                            this.#collectionKeyModalData(collection_key, collection_name, modal);
                            //DashboardBehavior.modalController(modal);
                        }
                    })
                }
            })
        });
        this.#modal_listing.forEach((modal) => {
            const close = modal.querySelector('div > label > img');
            if(close) {
                close.addEventListener('click',(e)=>{
                    DashboardBehavior.modalController(modal);
                })
            }
        });
        DashboardBehavior.dataDisplayer(this.#collections_links);
    }

    static modalController(modal) {
        if(modal.classList.contains('modal-hidden')) {
            modal.classList.add('modal-active');
            modal.classList.remove('modal-hidden');
        }
        else {
            modal.classList.add('modal-hidden');
            modal.classList.remove('modal-active');
        }
    }

    #collectionKeyModalData(collection_key, collection_name, modalElement) {
        const data = this.#collections_data[collection_name];

        const tbody = modalElement.querySelector('table > tbody');
        if(tbody) {
            tbody.innerHTML = '';
            const keys = data.keys;
            const keyIndex = keys.indexOf(collection_key);
            const tds = [
                collection_key,
                data.type[keyIndex],
                data.primary.indexOf(collection_key) >= 0 ? 'Yes' : 'No',
                data.unique.indexOf(collection_key) >= 0 ? 'Yes' : 'No',
                'remove'
            ];
            const tr = document.createElement('tr');
            tds.forEach((t)=>{
                const td = document.createElement('td');
                td.textContent = t;
                if(t === 'remove') {
                    td.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i>';
                    td.addEventListener('click',(e)=>{
                        DashboardBehavior.removeCollectionKey(collection_key, collection_name);
                    })
                }
                tr.appendChild(td);
            });

            tbody.appendChild(tr);
            modalElement.classList.remove('modal-hidden');
            modalElement.classList.add('modal-active')
        }
    }

    static removeCollectionKey(collection_key, collection_name) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST',window.location.href, true);
        xhr.onload = function () {
            if(this.status === 200) {
                const data = JSON.parse(this.responseText);
                if(data.status === true) {
                    document.location.reload();
                }
            }
        }
        xhr.send(JSON.stringify({collection_name, collection_key, action: 'remove-collection-key'}));
    }

    static dataDisplayer(collections_links) {
        if(collections_links) {
            Array.from(collections_links).forEach((collections_link)=>{
                const collection = collections_link.parentElement.parentElement;
                collection.addEventListener('click',(e)=>{
                    const collection_name = collection.querySelector('summary').textContent;
                    if(collection.open === false) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', window.location.href, true);
                        xhr.onload = function () {
                            if(this.status === 200) {
                                const data = JSON.parse(this.responseText);
                                DashboardBehavior.createCollectionDataTable(data);
                            }
                        }
                        xhr.send(JSON.stringify({collection_name,action: 'listing-data'}));
                    }
                })
            })
        }
    }

    static createCollectionDataTable(data) {

        const table = document.createElement('table');
        table.className = 'table table-stripped';
        const table_head = document.createElement('thead');
        const tr_head = document.createElement('tr');
        const tbody = document.createElement('tbody');
        table_head.appendChild(tr_head);
        table.appendChild(table_head);
        table.appendChild(tbody);

         ['INDEX', 'DATA', 'ACTION' ].forEach((item)=>{
             const th = document.createElement('th');
             th.textContent = item;
             tr_head.appendChild(th);
        });

        data.forEach((item,index)=>{
            const td1 = document.createElement('td');
            td1.textContent = index;
            const td2 = document.createElement('td');
            const para = document.createElement('p');
            para.className = "bordered p-3 rounded bg-light";
            para.innerHTML = JSON.stringify(item);
            td2.appendChild(para);
            const td3 = document.createElement('td');

            const tr = document.createElement('tr');
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tbody.appendChild(tr);
        });

        const collectionWrapper = document.getElementById('collection-data');
        if(collectionWrapper) {
            collectionWrapper.innerHTML = '';
            collectionWrapper.appendChild(table)
        }
    }
}
