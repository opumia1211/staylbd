@php
    $footerData   = getCachedFooterData();
    $companyInfo    = $footerData['footer_company_info'] ?? null;
    $socialElement  = $footerData['social_element'] ?? collect();
    $policyPages    = $footerData['policy_pages'] ?? collect();
    $quickLinks     = $footerData['footer_quick_links'] ?? collect();
    $footerContent  = $footerData['footer_content'] ?? null;
    $categories     = \App\Models\Category::active()->limit(6)->get();
@endphp

<footer class="bg-[#F8FAFC] pt-20 border-t border-gray-100 mt-20">
    <div class="container max-w-[1400px] mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 pb-20">
            <!-- Brand Column -->
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-block mb-8">
                    <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="Logo" class="h-10">
                </a>
                <p class="text-gray-500 font-medium leading-relaxed mb-8 max-w-sm">
                    {{ __($companyInfo->data_values->about_text ?? 'Premium multipurpose e-commerce platform.') }}
                </p>
                <div class="flex items-center gap-4">
                    @foreach ($socialElement as $social)
                        <a href="{{ $social->data_values->url ?? '#' }}" target="_blank" class="size-11 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:bg-zenis-primary hover:text-white hover:border-zenis-primary transition-all duration-300 shadow-sm">
                           {!! $social->data_values->icon !!}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Categories -->
            <div class="lg:col-span-2">
                <h4 class="text-gray-800 font-black uppercase tracking-widest text-sm mb-8">{{ __('Categories') }}</h4>
                <ul class="space-y-4">
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="text-gray-500 font-bold hover:text-zenis-primary transition-colors flex items-center gap-2 group">
                                <span class="w-2 h-0.5 bg-zenis-primary transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></span>
                                {{ __($cat->name) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Shop Links -->
            <div class="lg:col-span-2">
                <h4 class="text-gray-800 font-black uppercase tracking-widest text-sm mb-8">{{ __('Company') }}</h4>
                <ul class="space-y-4">
                    @foreach ($policyPages as $policy)
                        <li><a href="{{ route('policy.pages.short', $policy->id) }}" class="text-gray-500 font-bold hover:text-zenis-primary transition-colors">{{ __($policy->data_values->title) }}</a></li>
                    @endforeach
                    <li><a href="{{ route('contact') }}" class="text-gray-500 font-bold hover:text-zenis-primary transition-colors">{{ __('Contact Us') }}</a></li>
                </ul>
            </div>

            <!-- Newsletter Area -->
            <div class="lg:col-span-4">
                <h4 class="text-gray-800 font-black uppercase tracking-widest text-sm mb-8">{{ __('Newsletter') }}</h4>
                <p class="text-gray-500 font-medium mb-6">{{ __('Get updates on new products and special offers.') }}</p>
                <form action="{{ route('subscribe') }}" method="POST" class="relative">
                    @csrf
                    <input type="email" name="email" placeholder="{{ __('Your email address') }}" class="w-full bg-white border border-gray-100 rounded-xl px-6 py-4 text-sm focus:outline-none focus:border-zenis-primary transition-all shadow-sm">
                    <button type="submit" class="absolute right-2 top-2 bg-zenis-primary text-white p-2.5 rounded-lg hover:bg-opacity-90 transition-all">
                        <i class="hgi hgi-stroke hgi-send-01 text-xl"></i>
                    </button>
                </form>
                
                @if(!empty($companyInfo->data_values->contact_phone))
                <div class="mt-10 flex items-center gap-4">
                    <div class="size-12 rounded-full bg-zenis-secondary/10 text-zenis-secondary flex items-center justify-center">
                        <i class="hgi hgi-stroke hgi-call text-2xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Questions? Call us') }}</span>
                        <span class="text-lg font-black text-gray-800">{{ $companyInfo->data_values->contact_phone }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="bg-white border-t border-gray-100 py-8">
        <div class="container max-w-[1400px] mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-gray-500 font-bold text-sm">
                @php
                    $copyrightText = $footerContent && trim($footerContent->data_values->copyright_text ?? '') !== ''
                        ? $footerContent->data_values->copyright_text
                        : __('Copyright') . ' &copy; ' . date('Y') . ' ' . gs('site_name') . '. ' . __('All Right Reserved.');
                    echo str_replace('{year}', date('Y'), $copyrightText);
                @endphp
            </p>
            <div class="flex items-center gap-6">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_1.jpg" class="h-6 opacity-80" alt="Payments">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_2.jpg" class="h-6 opacity-80" alt="Payments">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_3.jpg" class="h-6 opacity-80" alt="Payments">
            </div>
        </div>
    </div>
</footer>
