@props([
    'options' => [],      // Array of options [{id: 1, name: 'Option 1'}, ...]
    'name' => '',         // Form field name
    'id' => null,         // Input ID (defaults to name if not provided)
    'selected' => '',     // Currently selected value
    'placeholder' => '',  // Placeholder text
    'searchPlaceholder' => '', // Search field placeholder
    'noResults' => '',    // Text to show when no results found
    'required' => false,  // Whether the field is required
    'disabled' => false,  // Whether the field is disabled
    'error' => false,     // Whether the field has an error
])

@php
    $id = $id ?? $name;
    
    // Find the display text for the selected option
    $displayText = $placeholder;
    foreach ($options as $option) {
        if (isset($option['id']) && $option['id'] == $selected) {
            $displayText = $option['name'];
            break;
        }
    }
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selected: '{{ $selected }}',
        displayText: '{{ $displayText }}',
        options: {{ json_encode($options) }},
        placeholder: '{{ $placeholder }}',
        searchPlaceholder: '{{ $searchPlaceholder }}',
        noResults: '{{ $noResults }}',
        
        init() {
            this.$watch('selected', value => {
                if (value) {
                    const selectedOption = this.options.find(option => option.id == value);
                    if (selectedOption) {
                        this.displayText = selectedOption.name;
                    }
                } else {
                    this.displayText = this.placeholder;
                }
            });
        },
        
        openDropdown() {
            this.open = true;
            // Focus the search input after the dropdown is shown
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },
        
        handleKeyDown(e) {
            // Open dropdown when down arrow is pressed
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.openDropdown();
            }
            // Close dropdown when escape is pressed
            else if (e.key === 'Escape') {
                e.preventDefault();
                this.open = false;
            }
            // Open dropdown and start typing when any letter is pressed
            else if (e.key.length === 1 && e.key.match(/[a-z0-9]/i)) {
                if (!this.open) {
                    this.search = e.key;
                    this.openDropdown();
                }
            }
        },
        
        focusFirstOption() {
            this.$nextTick(() => {
                // Get all visible option elements
                const options = this.$refs.optionsList.querySelectorAll('li a');
                if (options.length > 0) {
                    // Focus the first option
                    options[0].focus();
                }
            });
        },
        
        selectOption(optionId) {
            this.selected = optionId;
            this.open = false;
        }
    }"
    class="relative"
    @keydown.tab="open = false"
    @click.away="open = false"
>
    <div class="relative">
        <input
            type="text"
            class="input input-bordered w-full pr-10 mb-2 {{ $error ? 'input-error' : '' }}"
            :placeholder="placeholder"
            :value="displayText"
            @click="openDropdown()"
            @keydown="handleKeyDown"
            readonly="readonly"
            {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
        >
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>

        <input type="hidden" name="{{ $name }}" x-model="selected" {{ $required ? 'required' : '' }}>
        
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-1 w-full bg-base-100 shadow-lg rounded-md max-h-60 overflow-auto border dark:border-slate-600"
            @click.stop=""
            style="display: none;"
        >
            
            <div class="p-2">
                <input
                    x-ref="searchInput"
                    type="text"
                    class="input input-bordered w-full"
                    :placeholder="searchPlaceholder"
                    x-model="search"
                    @keydown.escape.prevent="open = false"
                    @keydown.enter.prevent="
                        const filtered = options.filter(o => o.name.toLowerCase().includes(search.toLowerCase()));
                        if (filtered.length === 1) {
                            selectOption(filtered[0].id);
                        }
                    "
                    @keydown.arrow-down.prevent="focusFirstOption()"
                >
            </div>
            
            <ul class="py-1" x-ref="optionsList">
                <template x-for="(option, index) in options.filter(o => o.name.toLowerCase().includes(search.toLowerCase()))" :key="option.id">
                    <li>
                        <a @click.prevent="selectOption(option.id)"
                           @keydown.enter.prevent="selectOption(option.id)"
                           @keydown.space.prevent="selectOption(option.id)"
                           @keydown.arrow-up.prevent="
                               if (index === 0) {
                                   $refs.searchInput.focus();
                               } else {
                                   const prevOption = $refs.optionsList.querySelectorAll('li a')[index-1];
                                   if (prevOption) prevOption.focus();
                               }
                           "
                           @keydown.arrow-down.prevent="
                               const nextOption = $refs.optionsList.querySelectorAll('li a')[index+1];
                               if (nextOption) nextOption.focus();
                           "
                           @keydown.escape.prevent="open = false"
                           class="block px-4 py-2 text-sm hover:bg-base-200 dark:hover:bg-base-300 cursor-pointer focus:outline-none focus:bg-base-200 dark:focus:bg-base-300"
                           :class="{ 'bg-base-200 dark:bg-base-300': selected == option.id }"
                           tabindex="0"
                           x-text="option.name"></a>
                    </li>
                </template>
                <li x-show="!options.some(o => o.name.toLowerCase().includes(search.toLowerCase())) && search !== ''">
                    <span class="block px-4 py-2 text-sm text-opacity-70" x-text="noResults"></span>
                </li>
            </ul>
        </div>
    </div>
</div> 
