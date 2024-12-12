<script setup>
import { ref } from 'vue'
import { Button } from '@/Components/button'

const newsText = ref('')
const lyricsOptions = ref([])
const isLoading = ref(false);

const generateLyrics = async () => {
  if (!newsText.value || newsText.value.length > 300) {
    alert('Digite um texto válido com até 300 caracteres!');
    return;
  }

  isLoading.value = true;

  try {
    const response = await axios.post('/generate-lyrics', { text: newsText.value });
    lyricsOptions.value = response.data.data;
  } catch (error) {
    console.error(error);
    alert('Erro ao gerar opções. Tente novamente.');
  } finally {
    isLoading.value = false;
  }
};

</script>

<template>
    <div class="container mx-auto py-10">
      <h1 class="text-4xl font-bold mb-5">Jingle App</h1>
      <p class="mb-5">Crie jingles personalizados a partir de notícias!</p>
  
      <form @submit.prevent="generateLyrics" class="space-y-4">
        <div>
          <label for="news-text" class="block text-sm font-medium text-gray-700">Digite sua notícia:</label>
          <textarea
            id="news-text"
            v-model="newsText"
            maxlength="300"
            placeholder="Digite sua notícia..."
            class="block w-full p-4 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300"
          ></textarea>
        </div>
        <Button type="submit">Gerar Opções</Button>
      </form>

      <div v-if="isLoading" class="mt-5 text-blue-500">Gerando letras...</div>
  
      <div v-if="lyricsOptions.length" class="mt-10">
        <h2 class="text-2xl font-semibold mb-4">Opções Geradas:</h2>
        <ul class="space-y-2">
          <li
            v-for="(option, index) in lyricsOptions"
            :key="index"
            class="p-4 border rounded-md"
          >
            {{ option.text }}
          </li>
        </ul>
      </div>
    </div>
  </template>
