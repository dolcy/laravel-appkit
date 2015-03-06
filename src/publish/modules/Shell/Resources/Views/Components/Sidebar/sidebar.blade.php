<div class="sidebar">
	@foreach ($sidebar_widgets as $widget)
		@include($widget['view'])
	@endforeach
</div>