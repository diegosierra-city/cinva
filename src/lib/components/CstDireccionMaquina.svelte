<script lang="ts">
 import { cookie_info, apiKey, userNow } from '../../store';
export let direccion:string
export let cliente_id:number

let listDireccion: Array<any>=[]
 const urlAPI = $apiKey.urlAPI;

 const loadDireccion = async (id:number) => {
  console.log(urlAPI +
				'?ref=load&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_clientes&id='+id)
await fetch(
			urlAPI +
				'?ref=load&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_clientes&id='+id
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');
    listDireccion=[]
				listDireccion = [...listDireccion,result['ciudad']+':'+result['direccion']]	
    if(result['direccion2']!=''){
    listDireccion = [...listDireccion,result['ciudad2']+':'+result['direccion2']]	 
    }	
    if(result['direccion3']!=''){
    listDireccion = [...listDireccion,result['ciudad3']+':'+result['direccion3']]	 
    }	
    if(result['direccion4']!=''){
    listDireccion = [...listDireccion,result['ciudad4']+':'+result['direccion4']]	 
    }		
			})
			.catch((error) => console.log(error.message));
 }

 $: loadDireccion(cliente_id)
 </script>


 <div>
 <select  bind:value={direccion} class="inputA w-44">
 {#each listDireccion as direccion, i}
   <!-- content here -->
   <option value={direccion}>{direccion}</option>
 {/each}
 </select>
 
 </div>


