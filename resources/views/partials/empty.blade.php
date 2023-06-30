<div class="empty-content">
	<i class="glyphicon @if (!empty($icon))glyphicon-{{ $icon }}@else glyphicon-ok @endif @if (!empty($extra_class)) {{ $extra_class }}@endif"></i>
	@if (!empty($empty_header))
		<h2>{{ $empty_header }}</h2>
	@endif
	@if (!empty($empty_text))
		<p>{{ __($empty_text) }}</p>
	@endif
</div>