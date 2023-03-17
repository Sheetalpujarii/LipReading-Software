#import libraries
import tensorflow as tf
import tensorflow.keras as keras
import cv2
import numpy as np
from skimage.transform import resize
from tensorflow.keras.preprocessing import image as image_utils
import imutils
import dlib 
import imageio
from imutils import face_utils
from tensorflow.keras.models import load_model
import os
from flask import Flask,request,render_template
words = ['Begin', 'Choose', 'Connection', 'Navigation', 'Next', 'Previous', 'Start', 'Stop', 'Hello', 'Web']
model = load_model('cnn-model.h5')
print("model loaded")
app = Flask(__name__,template_folder="templates")



def load_and_process(video):
    cap = video
    detector = dlib.get_frontal_face_detector()
    predictor = dlib.shape_predictor("shape_predictor_68_face_landmarks.dat")
    frames = []
    pad_array = [np.zeros((100, 100))]                            
    max_seq_length = 22
    currentFrame = 0
    while True:
            ret, frame = cap.read()
            if ret:
                image = imutils.resize(frame, width=500)
                gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
                faces = detector(gray, 1)
                for (i, face) in enumerate(faces):
                    shape = predictor(gray, face)
                    shape = face_utils.shape_to_np(shape)
                    name, i, j = 'mouth', 48, 68
                    (x, y, w, h) = cv2.boundingRect(np.array([shape[i:j]])) 
                    roi = gray[y:y+h, x:x+w]
                    roi = imutils.resize(roi, width = 250, inter=cv2.INTER_CUBIC)
                    cv2.imwrite('color_0'+str(currentFrame)+'.jpg', roi)
                    roi = resize(roi,(100,100))
                    roi = 255 * roi 
                    roi= roi.astype(np.uint8)
                    currentFrame += 1
                    if(currentFrame%2 == 1):
                    	frames.append(roi)
            else:
                break
           
    frames.extend(pad_array * (max_seq_length - len(frames)))        
    pred=[]
    pred.append(frames)
    pred = np.array(pred)
    pred = np.expand_dims(pred,axis=4)
    cap.release()
    return pred


def is_confidence_too_low(prediction):
    prediction_class = np.argmax(prediction, axis=1)
    return prediction[0][prediction_class[0]]<0.5


@app.route('/predict',methods=['GET'])
def predict_by_model():
  
    path=request.args.get("imgPath")
    print(path)
    video=cv2.VideoCapture(path)
    pred_images = load_and_process(video)
    prediction = model.predict(pred_images)
    prediction_class = np.argmax(prediction, axis=1)
    
    

    if(is_confidence_too_low(prediction)):
        result="Can you say again? Please"

    else:
        result=words[prediction_class[0]]
        
    return render_template("result.html",result=result)


if __name__ == '__main__':
    app.run(port='23352',  debug=True)






