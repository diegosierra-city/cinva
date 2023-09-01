<script lang="ts">
	import type { Temporada } from '$lib/types/Temporada';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import type { City } from '$lib/utilities/Cities';
	import { Cities } from '$lib/utilities/Cities';
	import type { ListFilterCiudad } from '$lib/types/List';

	import Fuse from 'fuse.js';


	let m_show: boolean = false;
	let message: Message;

	let listTemporadas: Array<Temporada> = [];
let listTemporadasEspecial: Array<Temporada> = [];
		const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	//// GET
	console.log(
		urlAPI +
			'?ref=load-list&user_id=' +
			$userNow.id +
			'&time_life=' +
			$userNow.user_time_life +
			'&token=' +
			$userNow.token +
			'&folder=ca_temporadas&orden=nombre'
	); //&campo=tipo&campoV=tecnico

	const loadTemporadas = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_temporadas&orden=temporada&campo=ciudad&campoV='
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listTemporadas = result;
				console.log('LoadTemporadas', listTemporadas);
			})
			.catch((error) => console.log(error.message));
	};


	const loadTemporadasEspeciales = async () => {
		console.log('especial',urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_temporadas&orden=temporada&campo=ciudad&campoV=-1')
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_temporadas&orden=temporada&campo=ciudad&campoV=-1'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listTemporadasEspecial = result;
				console.log('LoadTemporadasESpecial', listTemporadas);
			})
			.catch((error) => console.log(error.message));
	};
	

	let newTemporada: Temporada;
	newTemporada = {
		id: 0,
 temporada: '',
 ciudad_cod: 0,
 ciudad: '',
 fecha_inicial: '',
 fecha_final: ''
	};

	function addTemporada(type:string) {
		if(type!='especial'){
			newTemporada = { ...newTemporada, id: Date.now() };
	 listTemporadas = [...listTemporadas, newTemporada];	
		}else{
			newTemporada = { ...newTemporada, id: Date.now() };
	 listTemporadasEspecial = [...listTemporadasEspecial, newTemporada];	
		}
	

		newTemporada = {
	id: 0,
 temporada: '',
 ciudad_cod: 0,
 ciudad: '',
 fecha_inicial: '',
 fecha_final: ''
	}
		}

	const saveTemporadas = async (type:string) => {
		console.log(listTemporadas);
		let listFinal=[...listTemporadas]
		if(type=='especial'){
			listFinal=[...listTemporadasEspecial]
		}
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listFinal,
				folder: 'ca_temporadas',
				orden: 'temporada'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('GuardoListado', result);
				type=='especial'? loadTemporadasEspeciales : loadTemporadas
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true
			})
			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_temporadas';
	let showTemporada: boolean = false;
	let temporadaActual: Temporada = newTemporada;
	let positionEdit: number = -1;
	let showFicha: boolean = false;

	onMount(async() => {
		await loadTemporadas();
		await loadTemporadasEspeciales();
	});

	


	let filtroCiudades: any;
	let editar_ciudad: string = '';
	const options = {
		includeScore: true,
		useExtendedSearch: true,
		//keys: ['author', 'tags']
		keys: ['ciudad', 'codigo']
	};

	function handleSearchInput(event: any) {
		editar_ciudad = event.target.id;
		const fuse = new Fuse(Cities, options);
		filtroCiudades = fuse.search(event.target.value);
	}

	function handleCitySelect(cod: number, ciu: string, position: number) {
		//console.log('Cod',cod)
		listTemporadas[position].ciudad_cod = cod;
		listTemporadas[position].ciudad = ciu;
		filtroCiudades = [];
	}

	
</script>

<svelte:head>
	<title>Temporadas</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative w-full pb-6">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={()=>saveTemporadas('')}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addTemporada('')}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Temporada</button
			>
		</div>

		
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Temporada </th>
				<th scope="col" class=""> Inicio </th>
				<th scope="col" class=""> Final </th>
			<th scope="col" class="" />
			</thead>
			<tbody>
				{#if listTemporadas.length>0}
					
				{#each listTemporadas as temporada, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA w-24" placeholder="nombre"
								bind:value={temporada.temporada}
							/>
							</td>
						<td>
							<input
							type="date"
							class="inputA w-24" placeholder="Fecha"
							bind:value={temporada.fecha_inicial}
						/>
						</td>
						<td>
							<input
							type="date"
							class="inputA w-24" placeholder="Fecha"
							bind:value={temporada.fecha_final}
						/>
						</td>
						
						<td class="text-center">borrar</td>
					</tr>
				{:else}
					Sin registros
				{/each}

					{/if}
			</tbody>
		</table>

		
		<div class="flex mt-10">
			<button class="btn-green mr-2 flex" on:click={()=>saveTemporadas('especial')}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar Temporadas Especiales</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addTemporada('especial')}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Temporada Especial</button
			>
		</div>

		
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Temporada </th>
				<th scope="col" class=""> Ciudad </th>
				<th scope="col" class=""> Inicio </th>
				<th scope="col" class=""> Final </th>
			<th scope="col" class="" />
			</thead>
			<tbody>
				{#if listTemporadasEspecial.length>0}
					
				{#each listTemporadasEspecial as temporada, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA w-24" placeholder="nombre"
								bind:value={temporada.temporada}
							/>
							</td>

							<td class="relative">

								<input
								type="text"
								class="inputA w-48"
								id={'S' + temporada.id}
								on:input={handleSearchInput}
								bind:value={temporada.ciudad}
							/>

							{#if filtroCiudades?.length > 0 && editar_ciudad === 'S' + temporada.id}
								<div
									class="absolute z-10"
									style="height:135px; background: #ccc; overflow-y:auto; overflow-x:hidden padding:10px"
								>
									<ul>
										{#each filtroCiudades as ciudad}
											<li style="border-bottom:dotted 1px white">
												<button
													on:click={() =>
														handleCitySelect(ciudad.item.codigo, ciudad.item.ciudad, i)}
												>
													{ciudad.item.ciudad}
													<small><small>- {ciudad.item.departamento}</small></small>
												</button>
											</li>
										{/each}
									</ul>
								</div>
							{/if}

							<input
								type="hidden"
								class="inputA"
								bind:value={temporada.ciudad_cod}
								placeholder="nombre"
							/>

							</td>

						<td>
							<input
							type="date"
							class="inputA w-24" placeholder="Fecha"
							bind:value={temporada.fecha_inicial}
						/>
						</td>
						<td>
							<input
							type="date"
							class="inputA w-24" placeholder="Fecha"
							bind:value={temporada.fecha_final}
						/>
						</td>
						
						<td class="text-center">borrar</td>
					</tr>
				{:else}
					Sin registros
				{/each}

					{/if}
			</tbody>
		</table>
	</div>
</div>


{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
