<script>
	import './styles.css';
	import {userNow} from '../store'
	import ControlMenu from '$lib/components/ControlMenu.svelte';
</script>


	<main class="flex w-full m-0 p-0 items-start body-app" >
		
		{#if $userNow.id!=0}
			 <ControlMenu />
		{/if}
		<section class="w-full p-0" >
			<slot />
		</section>
	</main>

	



