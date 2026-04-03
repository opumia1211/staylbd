@extends($activeTemplate . 'layouts.frontend')
@section('content')

@php
    	$blogCaption = getContent('blog.content',true);
@endphp
<section class="pb-150 pt-60">
	<div class="container">
		
		<div class="row mb-none-30 justify-content-center">
			@foreach($blogs as $blog)
			<div class="col-lg-4 col-md-6 mb-30 wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.3s">
				<div class="blog-post hover--effect-1 has-link">
					<a href="{{ route('blog.details',[slug($blog->data_values->title),$blog->id]) }}" class="item-link"></a>
					<div class="blog-post__thumb">
						<img src="{{ getImage('assets/images/frontend/blog/'.$blog->data_values->image) }}" alt="image" class="w-100" loading="lazy" decoding="async">
					</div>
					<div class="blog-post__content">
						<h4 class="blog-post__title">{{ __($blog->data_values->title) }}</h4>
						<p>{{ strLimit(strip_tags($blog->data_values->description),80) }}</p>
						@include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'blog-post__icon'])
					</div>
				</div>
			</div>
			@endforeach
		</div>
		
            @if ($blogs->hasPages())
                <div class="row justify-content-center mb-30-none">
                    <div class="col-lg-12 mb-30">
                        {{ paginateLinks($blogs) }}
                    </div>
                </div>
            @endif
	</div>
</section>

@endsection