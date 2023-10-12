<script lang="ts">
	import { Countries } from '$lib/utilities/Countries';
	import { Proveedores } from '$lib/utilities/ListProveedores';
	import type {User} from '$lib/types/User'

	import { Clientes } from '$lib/utilities/ListClientes';
	import { Documents } from '$lib/utilities/ListDocuments';
	import { cookie_info, apiKey, userNow } from '../../store';
	import type { Archivo } from '$lib/types/Archivo';
	import type { Message } from '$lib/types/Message';
	import { onMount } from 'svelte';
	import { Moon } from 'svelte-loading-spinners';

	import type { City } from '$lib/utilities/Cities';
	import { Cities } from '$lib/utilities/Cities';
	import type { ListFilterCiudad, List } from '$lib/types/List';

	import Fuse from 'fuse.js';

	export let m_show: boolean;
	export let message: Message;

	export let showFicha: boolean;
	export let folder: string;
	export let elemento: any = {};

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;

	let logo:string

	let listElemento: Array<any>;
	listElemento = Object.entries(elemento);

	let listProveedores: Array<ListFilterCiudad>;
	let listVendedores: Array<List>

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
				'&folder=cinva_archivos&orden=id&campo=tabla&campoV='+folder+'&campo2=tabla_id&campoV2=' +
				elemento.id
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('archivos:', result);
				listArchivos = result;
				//busco en el listado para saber si algún objeto tiene ref === 'logo'
				let newLogo = listArchivos.find((archivo)=>archivo.ref.toLowerCase()==='logo')
				if(newLogo) logo=newLogo.archivo
			})
			.catch((error) => console.log(error.message));
	};

const loadVendedores = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list-filter&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_users&orden=nombre&campo=tipo&campoV=vendedor&campo2=tipo&campoV2=administrador&name=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('vendedores:', result);
				listVendedores = result;				
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
				'&folder=cinva_proveedores&campo=nombre'
		)
			.then((response) => response.json())
			.then((data) => {
				console.log('LoadProveedores', data);
				listProveedores = data;
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		if(folder=='cinva_clientes'){
		//loadArchivos();	
		loadVendedores();
		}else{
			//loadList()
		}
		
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
				// 
				console.log('upload:',result);
				if(result[0].upload){
					message = {
						title: 'Subir Archivo',
						text: result[0].upload,
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					uploadArchivo=false
					fileRef=''
					loadArchivos()
				}else{
					message = {
						title: 'Error Subir Archivo',
						text: result[0].error,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
					uploadArchivo=false
				}
					
			})
			.catch((error) => {
				console.log('error',error)
				message = {
						title: 'Error',
						text: error[0].error,
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

	$: console.log('ele',elemento)


	let filtroCiudades: any;
	let editar_ciudad:string = ''
	const options = {
		includeScore: true,
		useExtendedSearch: true,
		//keys: ['author', 'tags']
		keys: ['ciudad', 'codigo']
	};

	function handleSearchInput(event: any) {
		editar_ciudad=event.target.id
		const fuse = new Fuse(Cities, options);
		filtroCiudades = fuse.search(event.target.value);
	}

	function handleCitySelect(cod: number, ciu: string, position: number) {
	
	listElemento[position-1][1] = cod;
	listElemento[position][1] = ciu;
	//listElemento=[...listElemento]
		filtroCiudades = [];
	console.log('Cod',listElemento)	
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

	<h3>{elemento.nombre}</h3>
	{#if logo}
		<img src={`${urlFiles}archivos/${logo}`} alt="logo" class="w-40 rounded-md shadow-md border-1 border-silver">
	{/if}

	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
		{#each listElemento as item, i}
			{#if item[0] != 'id'}
				<!-- content here -->
				
					
					{#if (item[0] !== 'ma' && item[0] !== 'mi' && item[0] !== 'ju' && item[0] !== 'vi' && item[0] !== 'sa' && item[0] !== 'do' && item[0] !== 'cma' && item[0] !== 'cmi' && item[0] !== 'cju' && item[0] !== 'cvi' && item[0] !== 'csa' && item[0] !== 'cdo' && item[0] !== 'imagen')}

<div class="px-2">
					<!-- svelte-ignore a11y-label-has-associated-control -->
					{#if item[0] !== 'lu' && item[0] !== 'clu'}

					<label>{formatoTexto(item[0])} {item[0] == 'nombre' ? '/ Razón Social' : ''}</label>

					{/if}
					
						
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
						{:else if item[0] === 'tipo_cliente'}
						<select class="inputA" bind:value={item[1]}>
							{#each Clientes as cliente}
								<!-- content here -->
								<option value={cliente.cod}>{cliente.cliente}</option>
							{/each}
						</select>
					{:else if item[0] === 'activo'}
						<select class="inputA" bind:value={item[1]}>
							<option value={true}>Activo</option>
							<option value={false}>Inactivo</option>
						</select>
						{:else if item[0] === 'fecha_contacto' || item[0] === 'ultimo_contacto' }
						<input type="date" class="inputA" readonly bind:value={item[1]} >
						{:else if item[0] === 'cumpleaños' }
						<input type="date" class="inputA"  bind:value={item[1]} >
						{:else if item[0] === 'ciudad_cod'}
						<input type="text" class="inputA" id="ciudad_cod" bind:value={item[1]} readonly>
						{:else if item[0] === 'ciudad'}
						
						<input
						type="text"
						class="inputA"
						id="ciudad"
						on:input={handleSearchInput}
						bind:value={item[1]}
					/>

					{#if filtroCiudades?.length > 0}
					
						<div class="absolute z-10" style="height:135px; background: #ccc; overflow-y:auto; overflow-x:hidden padding:10px">
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
					{:else if item[0] === 'tipo_servicio'}
					<select class="inputA" bind:value={item[1]}>
						<option value="tour">Tour</option>
						<option value="traslado">Traslado</option>
					</select>
					{:else if item[0] === 'vendedor_id'}
					<select class="inputA" bind:value={item[1]}>
						{#if listVendedores}
							{#each listVendedores as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
						{/if}
					</select>
{:else if item[0] === 'proveedor'}
					<select class="inputA" bind:value={item[1]}>
						{#if listProveedores}
												{#each (listProveedores.filter(prov => prov.ciudad_cod == listElemento[1][1])) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
						{/if}
					</select>
					{:else if item[0] === 'proveedor2'}

					<select class="inputA" bind:value={item[1]}>
						{#if listProveedores}
												{#each (listProveedores.filter(prov => prov.ciudad_cod == listElemento[1][1])) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
						{/if}
					</select>	
					{:else if item[0] === 'proveedor3'}

					<select class="inputA" bind:value={item[1]}>
						{#if listProveedores}
												{#each (listProveedores.filter(prov => prov.ciudad_cod == listElemento[1][1])) as pro}
						<option value={pro.id}>{pro.name}</option>
					{/each}
						{/if}
					</select>	
					{:else if (item[0] === 'lu' || item[0] === 'clu')}
					<div class="dias_semana flex">
					<div>
						lu<br>
						<select class="inputA w-18" bind:value={listElemento[i][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>
					
					<div>
						ma<br>
						<select class="inputA w-18" bind:value={listElemento[i+1][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>

					<div>
						mi<br>
						<select class="inputA w-18" bind:value={listElemento[i+2][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>

					<div>
						ju<br>
						<select class="inputA w-18" bind:value={listElemento[i+3][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>

					<div>
						vi<br>
						<select class="inputA w-18" bind:value={listElemento[i+4][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>

					<div>
						sa<br>
						<select class="inputA w-18" bind:value={listElemento[i+5][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>

					<div>
						do<br>
						<select class="inputA w-18" bind:value={listElemento[i+6][1]}>
	<option value="si">Si</option>
	<option value="no">No</option>
</select>
					</div>
					
					</div>
					
					{:else if typeof item[1] === 'number'}
						<!-- content here -->
						<input type="number" class="inputA" bind:value={item[1]} />
					{:else}
						<!-- else content here -->
						<input type="text" class="inputA" bind:value={item[1]} />
					{/if}
</div>
						{/if}
				
			

			{/if}
		{:else}
			<!-- empty list -->
		{/each}
	</div>

	{#if folder!=='cinva_servicios'}
	
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
