<script setup>
//导入歌曲本地列表
import songFileLists from '@/stores/songs'


import Aplayer from 'vue3-aplayer'
import { ref, onMounted, defineComponent, nextTick } from 'vue'

import { getFolderFile } from '@/api/songs.js';

const zIndex = 3000;
const size = 'small';


/***文件夹 */
let fileTreeData = ref([]); //文件夹列表

const initMusic = ref(songFileLists[0].list[0]);//默认播放歌曲内容
const autoplayState = ref(false);//是否自动播放
const itemLists = ref(songFileLists[0].list);//默认播放列表
const aplayerRef = ref();


function SongClick(music, list, type) {
  type = type || 'ZJ';
  if (type == 'WJJ') {
    initMusic.value = music;
    itemLists.value = [];
    getFolderFile({ name: filePath }).then(res => {
    // res = JSON.parse(res);
      if (res.code == 200) {
        fileTreeData = res.data;
      }
    });//获取文件夹
  }
  if (type == 'ZJ') {
    initMusic.value = music;
    let _list = list.filter(item => item?.src);
    console.log(_list);
    itemLists.value = _list;
  }
  // aplayerRef.value.initAudio()
  // aplayerRef.value.setSelfAdaptingTheme();     
  aplayerRef.value.thenPlay();

  nextTick(() => {//更新列表初始值
    aplayerRef.value.shuffledList = aplayerRef.value.getShuffledList();
  })

}


let filePath = './songs';//目录
if (process.env.NODE_ENV === "production") {
  filePath = './music';//目录
} else {
  filePath = './music';//目录
}

let curWJJTitle = ref(null);//当前文件夹
let curfilePath = ref(null);
let parentFilePath = ref(null);
function openFileClick(song) {//点击文件夹
  curfilePath = song.src.replace(/\./g, '');//替换

  curWJJTitle = song.name;
  const str = song.src;
  parentFilePath = str.substring(0, str.lastIndexOf('/'));
  getFolderFile({ name: song.src }).then(res => {
    if (res.code == 200) {
      itemLists.value = [];
      fileTreeData = res.data;
    }
  });//获取文件夹
}
function goBackFileClick() {

  curfilePath = parentFilePath.replace(/\./g, '');//替换
  if (curfilePath == '') {
    ElMessage({
      message: '已经是第一层了！',
      type: 'warning',
    })
    return false;
  }
  // if(parentFilePath == '/music'){
  //   ElMessage({
  //     message: '已经是第一层了！',
  //     type: 'warning',
  //   })
  //   return false;
  // }
  getFolderFile({ name: parentFilePath }).then(res => {
    if (res.code == 200) {
      itemLists.value = [];
      fileTreeData = res.data;
      parentFilePath = parentFilePath.substring(0, parentFilePath.lastIndexOf('/'));
    }
  });//获取文件夹
}

//tab
// import { TabsPaneContext } from 'element-plus'
let aplayerListFolded = ref(true);
const tabActiveName = ref('ZJ');
const handleClick = (name) => {
  if (tabActiveName == 'WJJ') {
    // aplayerListFolded = true;
    curfilePath = filePath.replace(/\./g, '');

  } else {
    // aplayerListFolded = false;
  }
}


const musicCollapseModel = ref(0);
const childMusicCollapseModel = ref();

onMounted(() => {
  // console.log(aplayerRef);
  getFolderFile({ name: filePath }).then(res => {
    // res = JSON.parse(res);
    if (res.code == 200) {
      fileTreeData = res.data;
    }
  });//获取文件夹

})

</script>
<template>
  <el-config-provider :size="size" :z-index="zIndex">
    <div class="nxmusic">
      <header>
        <h2>音乐库</h2>
        <el-tabs v-model="tabActiveName" class="nxmusic-tabs" @tab-change="handleClick">

          <el-tab-pane label="专辑" name="ZJ">
          </el-tab-pane>
          <el-tab-pane label="文件夹" name="WJJ">
          </el-tab-pane>
        </el-tabs>


        <div class="main-aplayer">
          <!--listMaxHeight="150"-->
          <Aplayer :autoplay="autoplayState" :music="initMusic" ref="aplayerRef" :list="itemLists" :float="true"
            repeat="repeat-all" listMaxHeight="150" :listFolded="aplayerListFolded" />
        </div>

      </header>
      <main>

        <div class="main-right">
          <template v-if="tabActiveName == 'WJJ'">

            <div class="file-box">

              <div class="file-box-breadcrumb">

                <template v-if="parentFilePath != null">
                  <a @click="goBackFileClick" style="color: #409eff">返回</a>
                  <span>{{ curfilePath }}</span>
                </template>
                <template v-else>
                  {{ curfilePath }}
                </template>
                <!-- <el-breadcrumb separator="/">
                  <template v-if="parentFilePath!=null">
                    <el-breadcrumb-item><a @click="goBackFileClick">返回</a></el-breadcrumb-item>
                    <el-breadcrumb-item>{{curfilePath}}</el-breadcrumb-item>
                  </template>
                  <template v-else>
                    <el-breadcrumb-item>{{ filePath }}</el-breadcrumb-item>
                  </template>

                </el-breadcrumb> -->
              </div>


              <div v-for="(song, index) in fileTreeData" :key="song.id" class="w-list"
                style="margin:0 0 12px;cursor: pointer;font-weight: bold;">
                <template v-if="song.is_dir"><!--有目录-->
                  <p @click="openFileClick(song)">
                    <el-icon :size="16" color="#409eff" style="margin:0 5px 1px 10px;">
                      <nx-folder />
                    </el-icon>
                    <span>{{ song.name }}</span>
                  </p>

                </template>
                <template v-else><!---没有目录-->
                  <p @click="SongClick(song, [], 'WJJ')">
                    <el-icon :size="14" color="#303133" style="margin:0 5px 0px 10px;"><nx-Headset /></el-icon>
                    <span>{{ song.name }}</span>
                  </p>


                </template>

              </div>

            </div>





          </template>

          <template v-if="tabActiveName == 'ZJ'">
            <el-collapse v-model="musicCollapseModel" accordion>
              <el-collapse-item :name="index" v-for="(song, index) in songFileLists" :key="index">
                <template v-slot:title>

                  <el-icon :size="16" color="#409eff" style="margin:0 5px 1px 10px;">
                    <nx-menu />
                  </el-icon>

                  <el-text class="mx-1" type="primary" size="large" style="font-weight: bold;">{{
                    song.title }}</el-text><el-tag style="margin-left:10px">共{{ song.list.length }}首</el-tag>
                </template>

                <el-card class="music-box-card">
                  <div class="music-box-card-list" v-for="(slist, slindex) in song.list" :key="slindex">


                    <div v-if="!(slist.src == undefined)" @click="SongClick(slist, song.list)" class="item">
                      <el-icon :size="14" color="#303133" style="margin:0 5px 0px 10px;"><nx-Headset /></el-icon>
                      {{ slindex + 1 }}. {{ slist.title }}
                    </div>

                    <div v-else>
                      <el-collapse v-model="childMusicCollapseModel" accordion>
                        <el-collapse-item :name="'schild' + slist.title">
                          <template v-slot:title>

                            <div class="item"> <el-text class="mx-1" type="primary" style="font-weight: bold;">{{ slindex
                              + 1 }}.
                                {{
                                  slist.title }}
                              </el-text>
                              <el-tag style="margin-left:10px">共{{ slist.list.length }}首</el-tag>
                            </div>
                          </template>


                          <el-card class="music-box-card">
                            <div class="music-box-card-list" v-for="(schildlist, schildindex) in slist.list"
                              :key="schildindex">
                              <div class="item" @click="SongClick(schildlist, slist.list)">
                                <el-icon :size="14" color="#303133"
                                  style="margin:0 5px 0px 10px;"><nx-Headset /></el-icon>
                                {{ schildindex + 1 }}. {{ schildlist.title }}
                              </div>
                            </div>
                          </el-card>



                        </el-collapse-item>
                      </el-collapse>
                    </div>
                  </div>

                </el-card>
              </el-collapse-item>

            </el-collapse>
          </template>


        </div>


      </main>


    </div>
  </el-config-provider>
</template>


<style scoped>
.nxmusic {
  /* width: 600px; */
  width: 100%;
  margin: 0 auto;
  box-sizing: border-box;
  /* position: relative; */
}

.nxmusic header {
  /* position: fixed;
  top: 0; 
  left: 0;
  z-index: 1; */
  width: 100%;
  background-color: #181818;
}

.nxmusic-tabs {
  padding: 0 1rem;
  color: #fff;
}

:deep(.nxmusic-tabs .el-tabs__item) {
  color: #fff;
}

:deep(.nxmusic-tabs .el-tabs__item:hover),
:deep(.nxmusic-tabs .el-tabs__item.is-active) {
  color: #409eff;
  cursor: pointer;
}


.nxmusic .main-aplayer {
  padding: 0 1rem;

  /* max-height: 183px; */
}

:deep(.main-aplayer .aplayer .aplayer-list) {
  max-height: 173px !important;
  overflow-y: auto;
}

.nxmusic header h2 {
  font-size: 14px;
  font-weight: bold;
  padding: 1rem 1rem 0.5rem 1rem;
  color: #fff;
}

.nxmusic main {
  /* margin:230px 1rem 0;  */
  padding: 0 5px;
  margin: 0 1rem;
  border-radius: 5px;
  overflow-y: auto;
  height: calc(100vh - 290px);
}

.music-box-card-list .item {
  padding: 5px 0;
  cursor: pointer;
  font-size: 14px;
  /* border-bottom: 1px solid #eee; */
}

.music-box-card-list .item:hover {
  background-color: #efefef;
}

.file-box {
  border: 1px solid #ddd;
  background: #fff;

  color: #333;
  padding: 10px 0;
}

.file-box .w-list {
  border: 1px solid #eee;
  border-width: 1px 0;
  /* padding: 10px 0; */
}

.file-box .w-list p {
  line-height: 20px;
  padding: 10px 0;
}

/* :deep(.file-box .w-list p .el-icon){
   vertical-align: text-top; 
  margin-top: 5px !important;
} */
.file-box .w-list p span {
  display: inline-block;
  vertical-align: top;
  width: calc(100% - 32px)
}

.file-box-breadcrumb {
  padding: 10px 0 15px 10px;
}

.file-box-breadcrumb>a,
.file-box-breadcrumb>span {
  margin-right: 10px;
}

.file-box-breadcrumb>a {
  cursor: pointer;
}
</style>
