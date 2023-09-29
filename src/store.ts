import { writable } from "svelte/store";
import { browser } from "$app/environment";
import type { User } from "$lib/types/User";

//export let cookie_name
//export let cookie_value

export const cookiesLibrary = writable([]);

export const cookie_update = (cookie_name: string, cookie_value: string) => {
	if (cookie_value == '') {
		cookiesLibrary.subscribe((val) => {
			if (browser) {
				window.localStorage.removeItem(cookie_name)
			}
		})
	} else {
		//cookiesLibrary.subscribe((val) => browser && localStorage.setItem(cookie_name, cookie_value));///update	
		cookiesLibrary.subscribe(() => {
			if (browser) {
				window.localStorage.setItem(cookie_name, cookie_value)
			}
		})

	}

}

export const cookie_info = (name: string) => {
	//return browser && localStorage.getItem(name)
	if (browser) {
		return window.localStorage.getItem(name)
	}

}


export const moduleAdmin = writable('')//first module

export const userNow = writable({
	id: 0,
	nombre: '',
	documento: '',
	email: '',
	tipo: '',
	user_time_life: 0,
	token: '',
	activo: true
})


export const apiKey = writable({

	company_id: 1,
	tokenWeb: "f99dad2ac548a08d3c28d74783092234",
	company_name: "Cinva",
	money: "$",///comillas simples error
	urlAPI: "https://cinva.cityciudad.com/cinva-control/api/api-Control.php",
	urlFiles: "https://cinva.cityciudad.com/cinva-control/maker-files/"	
})

