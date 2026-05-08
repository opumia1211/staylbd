<script>
    document.addEventListener('DOMContentLoaded', function() {
        var products = {!! json_encode(\App\Models\Product::where('status', 1)->inRandomOrder()->limit(10)->get(['name', 'image', 'id'])->map(function($p) {
            $p->image_url = getImage(getFilePath('product') . '/' . $p->image, getFileSize('product'));
            return $p;
        })) !!};

        if (!products || products.length === 0) return;

        var cities = ['Dhaka', 'Chattogram', 'Sylhet', 'Rajshahi', 'Khulna', 'Barishal', 'Cumilla', 'Gazipur', 'Narayanganj', 'Mymensingh'];
        var names = ['Rahim', 'Karim', 'Sajjad', 'Mamun', 'Fahim', 'Nusrat', 'Sadia', 'Mehedi', 'Arif', 'Tariq', 'Hasan', 'Rubel'];
        var timeAgo = ['Just now', '2 minutes ago', '5 minutes ago', '10 minutes ago', '1 hour ago'];

        var popupHtml = `
            <div id="livePurchasePopup" class="stayl-live-purchase-popup">
                <img src="" alt="Product" id="lpImage" class="stayl-lp-img">
                <div class="stayl-lp-info">
                    <span id="lpName" class="stayl-lp-name">Someone bought a product</span>
                    <span id="lpTime" class="stayl-lp-time">Just now</span>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', popupHtml);
        var popup = document.getElementById('livePurchasePopup');
        if (!popup) return;

        function showPopup() {
            var p = products[Math.floor(Math.random() * products.length)];
            var city = cities[Math.floor(Math.random() * cities.length)];
            var name = names[Math.floor(Math.random() * names.length)];
            var time = timeAgo[Math.floor(Math.random() * timeAgo.length)];

            document.getElementById('lpImage').src = p.image_url;
            document.getElementById('lpName').innerHTML = `<b>${name}</b> in <b>${city}</b> recently bought<br><span style="color:var(--stayl-active-blue, #10b981)">${p.name.substring(0, 25)}...</span>`;
            document.getElementById('lpTime').textContent = time;

            popup.classList.add('show');
            setTimeout(function() { popup.classList.remove('show'); }, 5000);
        }

        setTimeout(function() {
            showPopup();
            setInterval(showPopup, 20000);
        }, 5000);
    });
</script>
