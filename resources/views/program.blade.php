<x-guest-layout>
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-jet-authentication-card-logo />
            </div>
            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                
            <div id="aplayer"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/aplayer@1.10.0/dist/APlayer.min.js"></script>
    <script>
        window.onload = function () {
            const ap2 = new APlayer({
                container: document.getElementById('aplayer'),
                // fixed: true,
                autoplay: false,
                audio: []
            });

            function addDiv() {
                var objTo = document.getElementById('list');
                var divtest = document.createElement("div");
                divtest.innerHTML = "new div";
                objTo.appendChild(divtest);
            }

            axios({
                method: 'get',
                url: '/api/programs/' + {{$program->id}}
            })
            .then(function (response) {
                let domain = 'https://729lyprog.net';
                // let domain = 'http://lywx2018.yongbuzhixi.com';
                // let domain = 'http://cdn-lystore.test.upcdn.net';
                // var objTo = document.getElementById('list');
                response.data.data.forEach(item => {
                    console.log(item)
                    // var divtest = document.createElement("div");
                    // divtest.innerHTML = item.description;
                    // objTo.appendChild(divtest);
                    const url = item.path
                    const audio = {
                        name: '【' + item.program_name +'】' + item.description,
                        artist: item.play_at,
                        url,
                        cover: 'https://cdn.ly.yongbuzhixi.com/images/programs/'+item.code+'_prog_banner_sq.jpg',
                    }
                    ap2.list.add(audio)
                })
                // document.getElementById('list').
            });

        }

    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer@1.10.1/dist/APlayer.min.css">
    <style>
        .prose ol > li::before {
            content: '';
        }
    </style>
</x-guest-layout>
