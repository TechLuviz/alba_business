@if(isset($aboutStory) && $aboutStory->is_active)
    <section class="about-story" id="story">
        <div class="container about-story-grid">
            @if($aboutStory->image)
                <div class="about-story-photo">
                    <div class="about-story-card">
                        <img src="{{ asset($aboutStory->image) }}" alt="{{ $aboutStory->person_name }}" loading="lazy">
                        <div class="about-story-person">
                            @if($aboutStory->person_name)<strong>{{ $aboutStory->person_name }}</strong>@endif
                            @if($aboutStory->person_role)<span>{{ $aboutStory->person_role }}</span>@endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="about-story-copy">
                @if($aboutStory->eyebrow)
                    <p class="about-story-eyebrow">{{ $aboutStory->eyebrow }}<span></span></p>
                @endif
                <h2>{{ $aboutStory->heading }} <em>{{ $aboutStory->heading_accent }}</em></h2>
                @if($aboutStory->paragraph_1)<p class="about-story-text">{{ $aboutStory->paragraph_1 }}</p>@endif
                @if($aboutStory->paragraph_2)<p class="about-story-text">{{ $aboutStory->paragraph_2 }}</p>@endif
                @if($aboutStory->quote)<blockquote class="about-story-quote">&ldquo;{{ $aboutStory->quote }}&rdquo;</blockquote>@endif
            </div>
        </div>
    </section>
@endif
