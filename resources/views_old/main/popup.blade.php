<div id="popup-container">
    <div id="popup-content">
        <button id="close-popup">Close</button>
        <img src="{{ url('img/popup.png') }}" height="100%" alt="Popup Image">
    </div>
</div>


@push('style')
    <style>
        #popup-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        #popup-content {
            background: #fff;
            padding: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            position: relative;
            text-align: center;
            max-width: 800px; /* Maximum width for the container */
            width: 100%;
            max-height: 100%;
            overflow: auto;
               margin: 0 auto;
            top: 1%;
        }

        #popup-content img {
            max-width: 100%; /* Make the image responsive */
            max-height: 100%; /* Make the image responsive */
        }

        #close-popup {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #8D12D1;
            color: #fff;
            border: none;
            border-radius: 5%;
            width: 80px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
            font-size: 20px;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            transition: background-color 0.3s, transform 0.2s;
        }

        #close-popup:hover {
            background-color: #9E29B3;
            transform: scale(1.1);
        }

        /* Media query for smaller screens (e.g., mobile devices) */
        @media (max-width: 768px) {
            #popup-container {
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
            }

            #popup-content {
                max-width: 100%;
            }
        }


    </style>
    @endpush

@push('script')
    <script>
        function showPopup() {
            const lastSeen = localStorage.getItem('popupLastSeen');
            if (!lastSeen) {
                // Popup hasn't been seen yet.
                document.getElementById('popup-container').style.display = 'block';
            } else {
                const lastSeenDate = new Date(lastSeen);
                const currentTime = new Date();
                const timeDiff = currentTime - lastSeenDate;
                const hoursSinceLastSeen = timeDiff / (1000 * 60 * 60);

                if (hoursSinceLastSeen >= 24) {
                    // Popup hasn't been seen in the last 24 hours.
                    document.getElementById('popup-container').style.display = 'block';
                }
            }
        }

        function closePopup() {
            document.getElementById('popup-container').style.display = 'none';
            localStorage.setItem('popupLastSeen', new Date().toString());
        }

        document.getElementById('close-popup').addEventListener('click', closePopup);

        window.addEventListener('load', showPopup);

    </script>
@endpush
