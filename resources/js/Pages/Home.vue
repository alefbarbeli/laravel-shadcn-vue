<script setup lang="ts">
import { ref } from 'vue'

import { Textarea } from '@/Components/textarea'
import { Label } from '@/Components/label'
import { Button } from '@/Components/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/Components/card'


const newsText = ref('')
const lyricsOptions = ref([])
const selectedLyric = ref(null)
const audioUrl = ref(null)
const isLoading = ref(false)


const clear = () => {
  newsText.value = '';
  lyricsOptions.value = [];
  selectedLyric.value = null;
  audioUrl.value = null;
  isLoading.value = false;
}

const generateLyrics = async () => {
  if (!newsText.value || newsText.value.length > 300) {
    alert('Digite um texto válido com até 300 caracteres!');
    return;
  }

  isLoading.value = true;

  try {
    const response = await axios.post('/generate-lyrics', { text: newsText.value });
    lyricsOptions.value = response.data.data;
    selectedLyric.value = null;
    audioUrl.value = null;
  } catch (error) {
    console.error(error);
    alert('Erro ao gerar opções. Tente novamente.');
  } finally {
    isLoading.value = false;
  }
}

const createJingle = async () => {
  if (!selectedLyric.value) {
    alert('Por favor, selecione uma letra!');
    return;
  }

  isLoading.value = true;

  try {
    const response = await axios.post('/generate-jingle', 
        { 
            selectedSentence: {
                text: selectedLyric.value.text,
                hash: selectedLyric.value.hash,
            }
        });
    audioUrl.value = response.data.audioUrl;
  } catch (error) {
    console.error(error);
    alert('Erro ao gerar o jingle. Tente novamente.');
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="container relative hidden h-[100vh] flex-col items-center justify-center md:grid lg:max-w-none lg:grid-cols-2 lg:px-0">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
      <div class="absolute inset-0 bg-zinc-900" />
      <div class="relative z-20 flex items-center text-lg font-medium">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
          class="mr-2 h-6 w-6"
        >
          <path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3" />
        </svg>
        Jingle Generator
      </div>
      <div class="relative z-20 mt-auto">
        <blockquote class="space-y-2">
          <p class="text-lg">
            &ldquo;Uma aplicação web interativa que transforma textos em jingles personalizados, permitindo aos usuários criar e ouvir composições únicas com facilidade.&rdquo;
          </p>
          <footer class="text-sm">
            Alef Barbeli
          </footer>
        </blockquote>
      </div>
    </div>
    <div class="lg:p-8">
      <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
        <div class="flex flex-col space-y-2 text-center">
          <h1 class="text-2xl font-semibold tracking-tight">
            Jingle App
          </h1>
          <p class="text-sm text-muted-foreground">
            Crie jingles personalizados a partir de um input de texto!
          </p>

          <Card class="p-4">
            <div v-if="audioUrl">
              <CardHeader>
                <CardTitle>Seu Jingle:</CardTitle>
              </CardHeader>

              <CardContent>
                <audio :src="audioUrl" controls class="w-full"></audio>
              </CardContent>

              <CardFooter class="justify-center">
                <Button variant="secondary" class="mt-5" @click="clear" :disabled="isLoading">
                  Recomeçar
                </Button>
              </CardFooter>
            </div>

            <div v-else-if="lyricsOptions && lyricsOptions.length">
              <CardHeader>
                <CardTitle>Opções Geradas:</CardTitle>
              </CardHeader>

              <CardContent>  
                <ul class="space-y-2">
                  <li
                    v-for="(option, index) in lyricsOptions"
                    :key="index"
                    @click="selectedLyric = option"
                    :class="['p-4 border rounded-md cursor-pointer', selectedLyric && selectedLyric === option ? 'bg-blue-100' : '']"
                    >
                    {{ option.text }}
                  </li>
                </ul>
              </CardContent>

              <CardFooter class="justify-center">
                <Button variant="outline" class="mt-5" @click="createJingle" :disabled="!selectedLyric || isLoading">
                  Criar Jingle
                </Button>
                <Button variant="secondary" class="mt-5" @click="clear" :disabled="isLoading">
                  Voltar
                </Button>
              </CardFooter>
            </div>

            <form v-else @submit.prevent="generateLyrics" class="space-y-4">
                <CardHeader>
                    <CardTitle>Digite seu texto...</CardTitle>
                </CardHeader>

                <CardContent>
                    <Textarea
                      id="texto"
                      placeholder="Digite seu texto..."
                      :disabled="isLoading"
                      v-model="newsText"
                    />
                </CardContent>

                <CardFooter class="justify-center">
                    <Button variant="outline" type="submit" :disabled="isLoading">Gerar Opções</Button>
                </CardFooter>
            </form>

            <div v-if="isLoading" class="mt-5 text-blue-500">Carregando...</div>
          </Card>
        </div>
        
        <p class="px-8 text-center text-sm text-muted-foreground">
          Esta é uma aplicação desenvolvida como test case para a agência
          <a
            href="https://270b.com/"
            target="_blank"
            class="underline underline-offset-4 hover:text-primary"
          >
            270B
          </a>
          .
        </p>
      </div>
    </div>
  </div>
</template>