<script lang="ts">
	import { Countries } from '$lib/utilities/Countries';
	import { Proveedores } from '$lib/utilities/ListProveedores';
	import { Clientes } from '$lib/utilities/ListClientes';
	import { Documents } from '$lib/utilities/ListDocuments';
	import { cookie_info, apiKey, userNow } from '../../store';
	import type { Archivo } from '$lib/types/Archivo';
	import type { Message } from '$lib/types/Message';
	import { onMount } from 'svelte';
	import { Moon } from 'svelte-loading-spinners';

	import type { City } from '$lib/utilities/Cities';
	import { Cities } from '$lib/utilities/Cities';
	import type { ListFilterCiudad } from '$lib/types/List';
	import type { Servicio } from '$lib/types/Servicio';

	import Fuse from 'fuse.js';

	export let m_show: boolean;
	export let message: Message;

	export let showFicha: boolean;
	export let folder: string;
	export let elemento: Servicio;

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;

	export let listProveedores: Array<ListFilterCiudad>;

	//$: console.log('ListElemento', listElemento);

	function formatoTexto(texto: string) {
		// Reemplazar los guiones bajos por espacios
		let textoConvertido = texto.replace(/_/g, ' ');
		// Convertir la primera letra de cada palabra a mayúscula
		textoConvertido = textoConvertido.replace(/\b\w/g, (c) => c.toUpperCase());
		return textoConvertido;
	}

	export let actualizarView: any;

	const saveElemento = async () => {
		//console.log('antes',listElemento)
		console.log('enviando::', elemento);

		await fetch(urlAPI + '?ref=save', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				request: elemento,
				folder: folder,
				id: elemento.id
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				actualizarView(result[0]);

				console.log('ok');
				console.log(result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
			})

			.catch((error) => console.log('error:', error.message));

		//  });
	};

	let listArchivos: Array<Archivo> = [];

	const loadArchivos = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_archivos&orden=id&campo=tabla&campoV=' +
				folder +
				'&campo2=tabla_id&campoV2=' +
				elemento.id
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('archivos:', result);
				listArchivos = result;
			})
			.catch((error) => console.log(error.message));
	};

	const loadList = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list-filter-ciudad&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=ca_proveedores&campo=nombre'
		)
			.then((response) => response.json())
			.then((data) => {
				console.log('LoadProveedores', data);
				listProveedores = data;
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		if (folder !== 'ca_servicios') {
			loadArchivos();
		}
		/* else{
			loadList()
		} */
	});

	let uploadArchivo = false;
	let fileArchivo: FileList;
	let fileRef: string = '';

	//

	function uploadArchivos() {
		if (fileRef === '') {
			message = {
				title: 'Error',
				text: 'El campo de ref es obligatorio',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
			return;
		}
		uploadArchivo = true;
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('ref', fileRef);
		dataArray.append('folder', folder);
		dataArray.append('folder_id', elemento.id.toString());
		dataArray.append('uploadFile', fileArchivo[0]);

		fetch(urlAPI + '?ref=upload-archivo', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				//
				console.log('upload:', result);
				if (result[0].upload) {
					message = {
						title: 'Subir Archivo',
						text: result[0].upload,
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					uploadArchivo = false;
					fileRef = '';
					loadArchivos();
				} else {
					message = {
						title: 'Error Subir Archivo',
						text: result[0].error,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
					uploadArchivo = false;
				}
			})
			.catch((error) => {
				console.log('error', error);
				message = {
					title: 'Error',
					text: error[0].error,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
				uploadArchivo = false;
			});
	}

	function deleteArchivos(archivo: string, id: number) {
		if (confirm('Deseas Borrar este archivo?')) {
			const dataArray = new FormData();
			dataArray.append('user_id', String($userNow.id));
			dataArray.append('time_life', String($userNow.user_time_life));
			dataArray.append('token', $userNow.token);
			dataArray.append('archivo', archivo);
			dataArray.append('id', String(id));

			fetch(urlAPI + '?ref=delete-archivo', {
				method: 'POST',
				body: dataArray
			})
				.then((response) => response.json())
				.then((result) => {
					//
					message = {
						title: 'Borrar Archivo',
						text: 'Se ha borrado el archivo',
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					loadArchivos();
				})
				.catch((error) => {
					message = {
						title: 'Error',
						text: error.message,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
					uploadArchivo = false;
				});
		}
	}

	$: console.log('ele', elemento);

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

	function handleCitySelect(cod: number, ciu: string) {
		elemento.ciudad_cod = cod;
		elemento.ciudad = ciu;
		//listElemento=[...listElemento]
		filtroCiudades = [];
	}

	import Editor from '@tinymce/tinymce-svelte';
	let conf = {
		toolbar: 'h1 h2 h3 bold  italic forecolor aligncenter alignjustify alignleft undo redo ',
		menubar: false,
		height: 200,
		width: '100%'
	};

	$: console.log('PP', listProveedores);
</script>

<button
	class="bg-black/70 fixed top-0 bottom-0 left-0 right-0 z-10 p-2 md:pd-8 overflow-auto"
	on:click|self={() => (showFicha = false)}
/>

<div class="absolute top-20 z-20 w-11/12 md:w-8/12 mx-auto bg-white p-2 sm:pd-10 rounded-lg">
	<div class="flex">
		<button
			class="btn-green mr-2"
			on:click={() => {
				showFicha = false;
				saveElemento();
			}}><i class="fa fa-save" /> guardar</button
		>
		<button class="btn-primary" on:click={() => (showFicha = false)}
			><i class="fa fa-close" /> cerrar</button
		>
	</div>
	<h3 class="text-primary">{`Ref. ${elemento.ref} - ${elemento.servicio}`}</h3>

	<h4>Datos Básicos</h4>
	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
		<div class="px-2">
			<div>Ref.</div>
			<input type="text" class="inputA" bind:value={elemento.ref}  />
		</div>

		<div class="px-2">
			<div>Ciudad</div>
			<input
				type="text"
				class="inputA"
				id="ciudad"
				on:input={handleSearchInput}
				bind:value={elemento.ciudad}
			/>

			{#if filtroCiudades?.length > 0}
				<div
					class="absolute z-10"
					style="height:135px; background: #ccc; overflow-y:auto; overflow-x:hidden padding:10px"
				>
					<ul>
						{#each filtroCiudades as ciudad}
							<li style="border-bottom:dotted 1px white">
								<button on:click={() => handleCitySelect(ciudad.item.codigo, ciudad.item.ciudad)}>
									{ciudad.item.ciudad}
									<small><small>- {ciudad.item.departamento}</small></small>
								</button>
							</li>
						{/each}
					</ul>
				</div>
			{/if}
		</div>

		<div class="px-2">
			<div>Tipo de Servicio</div>
			<select class="inputA" bind:value={elemento.tipo_servicio}>
				<option value="tour">Tour</option>
				<option value="traslado">Traslado</option>
				<option value="pasadia">Pasadía</option>
				<option value="full day">Full Day</option>
			</select>
		</div>

		<div class="px-2">
			<div>Nombre Servicio</div>
			<input type="text" class="inputA" bind:value={elemento.servicio} />
		</div>
	</div>

	<h4>Proveedores</h4>
	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
		<div class="px-2">
			<div>Proveedor</div>

			<select class="inputA" bind:value={elemento.proveedor}>
				{#if listProveedores}
					{#each listProveedores.filter((prov) => +prov.ciudad_cod === +elemento.ciudad_cod) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
				{/if}
			</select>
		</div>

		<div class="px-2">
			<div>Proveedor 2</div>

			<select class="inputA" bind:value={elemento.proveedor2}>
				<option value="0">Opcional</option>
				{#if listProveedores}
					{#each listProveedores.filter((prov) => +prov.ciudad_cod === +elemento.ciudad_cod) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
				{/if}
			</select>
		</div>

		<div class="px-2">
			<div>Proveedor 3</div>

			<select class="inputA" bind:value={elemento.proveedor3}>
				<option value="0">Opcional</option>
				{#if listProveedores}
					{#each listProveedores.filter((prov) => +prov.ciudad_cod === +elemento.ciudad_cod) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
				{/if}
			</select>
		</div>
	</div>

	<h4>Precios</h4>
	<div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 text-xs whitespace-nowrap text-center precios">
		<div class="px-2">
			<div>1 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax1} />
		</div>

		<div class="px-2">
			<div>2 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax2} />
		</div>

		<div class="px-2">
			<div>3 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax3} />
		</div>

		<div class="px-2">
			<div>4-6 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax4} />
		</div>

		<div class="px-2">
			<div>7-9 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax7} />
		</div>

		<div class="px-2">
			<div>10-14 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax10} />
		</div>

		<div class="px-2">
			<div>15-19 Pax USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax15} />
		</div>

		<div class="px-2">
			<div>Grupo +20 USD</div>
			<input type="number" class="inputA" bind:value={elemento.pax20} />
		</div>
	</div>

	<div>
		Niños USD: <input type="number" class="inputA w-24 text-right" bind:value={elemento.children} />
	</div>

	<h4>Días de Operación</h4>
	<div class="dias_semana flex">
		<div class="px-2 text-center">
			lu<br />
			<select class="inputA w-18" bind:value={elemento.lu}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			ma<br />
			<select class="inputA w-18" bind:value={elemento.ma}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			mi<br />
			<select class="inputA w-18" bind:value={elemento.mi}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			ju<br />
			<select class="inputA w-18" bind:value={elemento.ju}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			vi<br />
			<select class="inputA w-18" bind:value={elemento.vi}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			sa<br />
			<select class="inputA w-18" bind:value={elemento.sa}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			do<br />
			<select class="inputA w-18" bind:value={elemento.do}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>
	</div>

	<h4>En Compartido</h4>
	<div>
		USD: <input type="number" class="inputA w-24 text-right" bind:value={elemento.compartido} />
	</div>

	<div class="dias_semana flex">
		<div class="px-2 text-center">
			lu<br />
			<select class="inputA w-18" bind:value={elemento.clu}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			ma<br />
			<select class="inputA w-18" bind:value={elemento.cma}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			mi<br />
			<select class="inputA w-18" bind:value={elemento.cmi}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			ju<br />
			<select class="inputA w-18" bind:value={elemento.cju}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			vi<br />
			<select class="inputA w-18" bind:value={elemento.cvi}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			sa<br />
			<select class="inputA w-18" bind:value={elemento.csa}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>

		<div class="px-2 text-center">
			do<br />
			<select class="inputA w-18" bind:value={elemento.cdo}>
				<option value="si">Si</option>
				<option value="no">No</option>
			</select>
		</div>
	</div>

	<h4>Información</h4>
	<div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2">
		<div class="px-2">
			<div>Descripción</div>
			<Editor
				apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
				bind:value={elemento.descripcion}
				{conf}
			/>
		</div>

		<div class="px-2">
			<div>Incluye</div>
			<Editor
				apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
				bind:value={elemento.incluye}
				{conf}
			/>
		</div>

		<div class="px-2">
			<div>No Incluye</div>
			<Editor
				apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
				bind:value={elemento.no_incluye}
				{conf}
			/>
		</div>

		<div class="px-2">
			<div>Observaciones</div>
			<Editor
				apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
				bind:value={elemento.observaciones}
				{conf}
			/>
		</div>
	</div>

	{#if folder !== 'ca_servicios'}
		<div class="mt-4">
			<h4>Archivos adjuntos:</h4>
			<small>*Máximo 10 Megas</small>
			<div class="flex">
				<span class="mt-2">Nuevo: </span>{#if uploadArchivo}
					<Moon size="40" unit="px" duration="4s" />
				{:else}
					<input type="file" class="inputA w-32" bind:files={fileArchivo} />
				{/if}

				<span class="mt-2">Referencia:</span>
				<input type="text" class="inputA w-40" bind:value={fileRef} />
				<button
					class="btn-primary mt-2"
					on:click={() => {
						uploadArchivos();
					}}><i class="fa fa-save" /> Guardar</button
				>
			</div>

			{#if listArchivos.length > 0}
				<!-- content here -->
				<table class="mt-4 w-11/12 md:w-8/12 mx-auto">
					<thead>
						<th scope="col" class="" />
						<th scope="col" class=""> Ref </th>
						<th scope="col" class=""> Fecha </th>
						<th scope="col" class=""> Archivo </th>
						<th scope="col" class="" />
					</thead>

					{#each listArchivos as archivo, i}
						<!-- content here -->
						<tr>
							<td>{i + 1}</td>
							<td>{archivo.ref}</td>
							<td>{archivo.fecha}</td>
							<td>
								<a
									href={`${urlFiles}archivos/${archivo.archivo}`}
									target="_blank"
									rel="noopener noreferrer">Abrir</a
								>
							</td>
							<td class="text-center text-red">
								<button on:click={() => deleteArchivos(archivo.archivo, archivo.id)}
									><i class="fa fa-trash" /></button
								>
							</td>
						</tr>
					{/each}
				</table>
			{/if}
		</div>
	{/if}

	<div class="flex mt-4">
		<button
			class="btn-green mr-2"
			on:click={() => {
				showFicha = false;
				saveElemento();
			}}><i class="fa fa-save" /> guardar</button
		>
		<button class="btn-primary" on:click={() => (showFicha = false)}
			><i class="fa fa-close" /> cerrar</button
		>
	</div>
</div>

<style>
	.precios input {
		text-align: right;
	}
</style>
