<footer class="footer">
    <div class="container">
        <div class="col-12 col-sm-4 col-md-3">
            <h6 class="footer__title">{{ trans('contact') }}</h6>
            <ul class="footer__list">
                <li><a href="tel:{{ config('config.contact_phone')}}">{{ config('config.contact_phone')}}</a></li>
                <li><a href="mailto:{{ config('config.contact_email')}}">{{ config('config.contact_email')}}</a></li>
            </ul>
            <ul class="footer__social">
                <li class="facebook"><a href="#"><i class="icon ion-logo-facebook"></i></a></li>
                <li class="instagram"><a href="#"><i class="icon ion-logo-instagram"></i></a></li>
                <li class="twitter"><a href="#"><i class="icon ion-logo-twitter"></i></a></li>
                <li class="vk"><a href="#"><i class="icon ion-logo-vk"></i></a></li>
            </ul>
        </div>
        @if (auth()->check())
            <div class="col-6 col-sm-6">
                <form action="{{ route('sendRequest') }}" method="POST" class="profile__form">
                    @csrf
                    <h1 class="footer__title">{{ trans('send_request') }} </h1>
                    @error ('content')
                        <span class="card__category"><a>{{ $message }}</a></span>
                    @enderror
                    <textarea id="text" class="form__textarea" placeholder="{{ trans('write') }}" name="content">{{ old('content') }}</textarea>
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <button class="profile__btn" type="submit">{{ trans('send') }}</button>
                </form>
            </div>
        @endif
        <div class="col-12">
            <div class="footer__copyright">
                <small>{{ trans('created_by') . " " . config('config.user_created_by') }}</small>
            </div>
        </div>
    </div>
</footer>
