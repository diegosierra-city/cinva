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

	export let m_show: boolean;
	export let message: Message;

	export let showFicha: boolean;
	export let folder: string;
	export let elemento: any = {};

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;

	let listElemento: Array<any>;
	listElemento = Object.entries(elemento);

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
		/* 		interface Objeto {
  [key: string]: any;
}

const newElemento: Objeto[] = listElemento.slice(1).map(function(subArray) {
  const objeto: Objeto = {};
  listElemento[0].forEach(function(nombrePropiedad:string, indice:number) {
    objeto[nombrePropiedad] = subArray[indice];
  });
  return objeto;
}); */

		const obj: { [key: string]: string } = {};

		for (let i = 0; i < listElemento.length; i++) {
			obj[listElemento[i][0]] = listElemento[i][1];
		}

		const newElemento: { [key: string]: string }[] = [obj];

		//console.log('antes',listElemento)
		console.log('enviando::', newElemento[0]);

		await fetch(urlAPI + '?ref=save', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				request: newElemento[0],
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
				'&folder=ca_archivos&orden=id&campo=tabla&campoV=ca_proveedores&campo2=tabla_id&campoV2=' +
				elemento.id
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('archivos:', result);
				listArchivos = result;
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		loadArchivos();
	});


	let uploadArchivo=false
	let fileArchivo: FileList
	let fileRef:string = ''

	//


	function uploadArchivos() {
		if(fileRef===''){
			message = {
						title: 'Error',
						text: 'El campo de ref es obligatorio',
						class: 'message-red',
						accion: ''
					};
					m_show = true;
			return
		}
		uploadArchivo=true;	
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('ref', fileRef);
		dataArray.append('folder', folder);
		dataArray.append('folder_id', elemento.id);
		dataArray.append('uploadFile', fileArchivo[0]);
	
		fetch(urlAPI + '?ref=upload-archivo',{
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:',result);
					message = {
						title: 'Subir Archivo',
						text: 'Se ha subido el archivo',
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					uploadArchivo=false
					fileRef=''
					loadArchivos()
			})
			.catch((error) => {
				message = {
						title: 'Error',
						text: error.message,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
					uploadArchivo=false
			});
	}


	
	function deleteArchivos(archivo:string, id:number) {
 if(confirm('Deseas Borrar este archivo?')){

		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('archivo', archivo);
		dataArray.append('id', String(id));
	
		fetch(urlAPI + '?ref=delete-archivo',{
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
					loadArchivos()
			})
			.catch((error) => {
				message = {
						title: 'Error',
						text: error.message,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
					uploadArchivo=false
			});

	}


	}
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

	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
		{#each listElemento as item}
			{#if item[0] != 'id'}
				<!-- content here -->
				<div class="px-2">
					<!-- svelte-ignore a11y-label-has-associated-control -->
					<label>{formatoTexto(item[0])} {item[0] == 'nombre' ? '/ Razón Social' : ''}</label>

					{#if item[0] === 'tipo_documento' || item[0] === 'titular_tipo_documento'}
						<select class="inputA" bind:value={item[1]}>
							{#each Documents as documento}
								<!-- content here -->
								<option value={documento.cod}>{documento.doc}</option>
							{/each}
						</select>
					{:else if item[0] === 'pais'}
						<select class="inputA" bind:value={item[1]}>
							{#each Countries as pais}
								<!-- content here -->
								<option value={pais.iata}>{pais.pais}</option>
							{/each}
						</select>
					{:else if item[0] === 'tipo_proveedor'}
						<select class="inputA" bind:value={item[1]}>
							{#each Proveedores as proveedor}
								<!-- content here -->
								<option value={proveedor.cod}>{proveedor.proveedor}</option>
							{/each}
						</select>
					{:else if item[0] === 'activo'}
						<select class="inputA" bind:value={item[1]}>
							<option value="true">Activo</option>
							<option value="false">Inactivo</option>
						</select>
					{:else if typeof item[1] === 'number'}
						<!-- content here -->
						<input type="number" class="inputA" bind:value={item[1]} />
					{:else}
						<!-- else content here -->
						<input type="text" class="inputA" bind:value={item[1]} />
					{/if}
				</div>
			{/if}
		{:else}
			<!-- empty list -->
		{/each}
	</div>

	<div class="mt-4">
		<h3>Archivos adjuntos:</h3>
		<small>*Máximo 10 Megas</small>
		<div class="flex">
			<span class="mt-2">Nuevo: </span>{#if uploadArchivo}
			<Moon size="40" unit="px" duration="4s" />
			{:else}
			<input type="file" class="inputA w-32" bind:files={fileArchivo}  />
{/if}

<span class="mt-2">Referencia:</span>
			<input type="text" class="inputA w-40" bind:value={fileRef} /> <button class="btn-primary mt-2" on:click={() => {
				uploadArchivos();
			}}><i class="fa fa-save" /> Guardar</button>
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
						<td class="text-center text-red"> <button on:click={()=> deleteArchivos(archivo.archivo, archivo.id)}><i class="fa fa-trash" /></button> </td>
					</tr>
				{/each}
			</table>
		{/if}
	</div>

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
