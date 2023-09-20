<script lang="ts">
	import type { HotelTarifas } from '$lib/types/HotelTarifas';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import Fuse from 'fuse.js';
	import type { City } from '$lib/utilities/Cities';
	import type {Temporada} from '$lib/types/Temporada'
	import { Cities } from '$lib/utilities/Cities';

	let m_show: boolean = false;
	let message: Message;

	let listHotelTarifas: Array<HotelTarifas> = [];
		let listHoteles: Array<any>
			let listTemporadas: Array<Temporada>

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	
	const loadHotelTarifas = async (city:number) => {

		console.log(
urlAPI +
				'?ref=load-list-tarifas&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&city='+city
		)
		await fetch(
			urlAPI +
				'?ref=load-list-tarifas&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&city='+city
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listHotelTarifas = result[0];
listHoteles = result[1];
listTemporadas = result[2];
				console.log(listHotelTarifas);
			})
			.catch((error) => console.log(error.message));
	};

	let city:number = 0
	let listCities: Array<any> =[]
	const loadCities = async () => {
		console.log(
urlAPI +
				'?ref=load-list-tarifas-cities&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token 
		)
		await fetch(
			urlAPI +
				'?ref=load-list-tarifas-cities&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('ciudades:');
listCities = result;
			})
			.catch((error) => console.log(error.message));
	};

	let newHotel: HotelTarifas;
	newHotel = {
		id: 0,
  hotel_id: 0,	
 temporada_id: 0,	
 pax: 0,	
 pax1: 0,	
 pax2: 0,	
 pax3: 0,	
 pax4: 0,	
 nino: 0,	
 bebe: 0,
	};

	
	const saveHotelTarifas = async () => {
		
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: [...listHotelTarifas],
				folder: 'ca_hotel_tarifas',
				orden: 'id'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log('GuardoListado', result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				updateExcel();
				//++listHotelTarifas = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'ca_hotel_tarifas';
	let showHotel: boolean = false;
	let hotelActual: HotelTarifas = newHotel;
	let positionEdit: number = -1;
	let showFicha: boolean = false;

	
	function actualizarView(p: HotelTarifas) {
		listHotelTarifas[positionEdit] = p;
		updateExcel();
	}

	const updateExcel = async () => {
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listHotelTarifas,
				folder: 'ca_hotel_tarifas',
				orden: 'id',
				archivo: 'excelHotelTarifas.xlsx'
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('excel', result);
			})
			.catch((error) => console.log(error.message));
	};

	//$: console.log('Hotel:',hotelActual)
	let uploadExcel = false;
	let fileExcel: FileList;
	let file: any;

	function upload() {
		uploadExcel = true;
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelHotelTarifas.xlsx');
		dataArray.append('folder', 'ca_hotel_tarifas');
		dataArray.append('uploadFile', fileExcel[0]);

		fetch(urlAPI + '?ref=upload-excel&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:', result);
				message = {
					title: 'Subir Archivo',
					text: 'Se ha subido el archivo y se ha actualizado la base de datos',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				uploadExcel = false;
				//loadHotelTarifas();
			})
			.catch((error) => console.log(error.message));
	}

	


	onMount(()=>{
		loadCities()
	})

	$: loadHotelTarifas(city)
	
</script>

<svelte:head>
	<title>HotelTarifas</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative pb-6  w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveHotelTarifas}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			

		</div>

		<div class="flex">
			<a
				href="https://goodtripscolombia.com/ca/api/api-CA.php?ref=download&archivo=4"
				target="_blank"
			>
				<button class="btn-primary mr-4 flex">
					<i class="fa fa-download mt-1 mr-2" />
					Descargar Excel</button
				></a
			>

			{#if uploadExcel}
				<Moon size="40" unit="px" duration="4s" />
			{/if}

			<button
				class="btn-primary px-2 text-xs"
				on:click={() => {
					file.click();
				}}
			>
				<i class="fa fa-upload mx-2" /> Subir Excel
			</button>

			<input
				type="file"
				accept=".xlsx"
				class="inputA w-32 hidden"
				placeholder="Subir Excel"
				bind:this={file}
				bind:files={fileExcel}
				on:change={() => {
					uploadExcel = true;
					upload();
				}}
			/>
		</div>

		<div>Ciudad: <select bind:value={city}  class="inputA w-40">
			<option value={0}>Selecciona</option>
		{#each listCities as ct}
			<option value={ct.ciudad_cod}>{ct.ciudad}</option>
		{/each}
		</select>
	</div>
		<table>
			<thead>
				<th scope="col" class="" />
				<th scope="col" class=""> Hotel </th>
				<th scope="col" class=""> Temporada </th>

				<th scope="col" class=""> Tarifa Unica x Persona </th>
				<th scope="col" class=""> Individual </th>
				<th scope="col" class=""> Doble </th>
				<th scope="col" class=""> Triple </th>
				<th scope="col" class=""> Cuadruple </th>
				<th scope="col" class=""> niño </th>
				<th scope="col" class=""> bebe </th>
			</thead>
			<tbody>
				{#each listHotelTarifas as hotel, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
						{listHoteles[hotel.hotel_id]}
						</td>
						<td>
							{listTemporadas[hotel.temporada_id]}
						</td>
					
						<td class="relative">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.pax}
							/>

							
						</td>
						<td>
							<input
								type="number"
								class="inputA"
								bind:value={hotel.pax1}
							/>
						</td>
						<td class="">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.pax2}
							/>
						</td>
<td class="">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.pax3}
							/>
						</td>
						<td class="">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.pax4}
							/>
						</td>
						<td class="">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.nino}
							/>
						</td>
						<td class="">
							<input
								type="number"
								class="inputA"
								bind:value={hotel.bebe}
							/>
						</td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>
</div>



{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
