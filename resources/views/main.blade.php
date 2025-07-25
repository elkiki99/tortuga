<x-layouts.app :title="__('Tortuga • Second Hand')">
    <!-- Header section -->
    <section class="relative w-full h-full overflow-hidden">
        <div x-data="{
            slides: [{
                    imgSrc: 'https://penguinui.s3.amazonaws.com/component-assets/carousel/aspect-slide-1.webp',
                    imgAlt: 'New collection - ride the wave of excitement.',
                },
                {
                    imgSrc: 'https://penguinui.s3.amazonaws.com/component-assets/carousel/aspect-slide-2.webp',
                    imgAlt: 'Up to 30% discount, gear up for adventure.',
                },
        
                {
                    imgSrc: 'https://penguinui.s3.amazonaws.com/component-assets/carousel/aspect-slide-3.webp',
                    imgAlt: '30% off all surfing essentials, ride like a pro.',
                },
            ],
            currentSlideIndex: 1,
            previous() {
                if (this.currentSlideIndex > 1) {
                    this.currentSlideIndex = this.currentSlideIndex - 1
                } else {
                    // If it's the first slide, go to the last slide           
                    this.currentSlideIndex = this.slides.length
                }
            },
            next() {
                if (this.currentSlideIndex < this.slides.length) {
                    this.currentSlideIndex = this.currentSlideIndex + 1
                } else {
                    // If it's the last slide, go to the first slide    
                    this.currentSlideIndex = 1
                }
            },
        }" class="relative w-full overflow-hidden">

            <!-- previous button -->
            <button type="button"
                class="absolute left-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-surface/40 p-2 text-on-surface transition hover:bg-surface/60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:outline-offset-0 dark:bg-surface-dark/40 dark:text-on-surface-dark dark:hover:bg-surface-dark/60 dark:focus-visible:outline-primary-dark"
                aria-label="previous slide" x-on:click="previous()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none"
                    stroke-width="3" class="size-5 md:size-6 pr-0.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <!-- next button -->
            <button type="button"
                class="absolute right-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-surface/40 p-2 text-on-surface transition hover:bg-surface/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:outline-offset-0 dark:bg-surface-dark/40 dark:text-on-surface-dark dark:hover:bg-surface-dark/60 dark:focus-visible:outline-primary-dark"
                aria-label="next slide" x-on:click="next()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none"
                    stroke-width="3" class="size-5 md:size-6 pl-0.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <!-- slides -->
            <!-- Change aspect-3/1 to match your images aspect ratio -->
            <div class="relative aspect-21/9  w-full">
                <template x-for="(slide, index) in slides">
                    <div x-cloak x-show="currentSlideIndex == index + 1" class="absolute inset-0"
                        x-transition.opacity.duration.700ms>
                        <img class="absolute w-full h-full inset-0 object-cover text-on-surface dark:text-on-surface-dark"
                            x-bind:src="slide.imgSrc" x-bind:alt="slide.imgAlt" />
                    </div>
                </template>
            </div>

            <!-- indicators -->
            <div class="absolute rounded-radius bottom-3 md:bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-4 md:gap-3 bg-surface/75 px-1.5 py-1 md:px-2 dark:bg-surface-dark/75"
                role="group" aria-label="slides">
                <template x-for="(slide, index) in slides">
                    <button class="size-2 rounded-full transition bg-on-surface dark:bg-on-surface-dark"
                        x-on:click="currentSlideIndex = index + 1"
                        x-bind:class="[currentSlideIndex === index + 1 ? 'bg-on-surface dark:bg-on-surface-dark' :
                            'bg-on-surface/50 dark:bg-on-surface-dark/50'
                        ]"
                        x-bind:aria-label="'slide ' + (index + 1)"></button>
                </template>
            </div>
        </div>
    </section>

    <!-- Novedades section -->
    <livewire:sections.novedades />
</x-layouts.app>
